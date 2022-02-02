<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Flat;
use App\Models\Pull;
use App\Models\Tarif;
use App\Models\User;
use App\Services\CalcService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pokaz;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    private $calcService;

    public function __construct(CalcService $calcService)
    {
        $this->calcService = $calcService;
    }

    public function index()
    {
        return view('admin.index');
    }

    public function adminCreate()
    {
        if (!Auth::user()->is_manager && !Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $result = Pokaz::getRepPeriodAdmin();

        //$user = User::factory()->create();
        //dd($user);

        return view('admin.pokaz.create', [
            'rep_month' => $result['rep_month'],
            'rep_year' => $result['rep_year'],
        ]);
    }

    public function adminStore(Request $request)
    {
        if (!Auth::user()->is_manager || !Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $rules = [
            'flat' => 'required|string|max:3|regex:#^[0-9А-Яа-я]+$#',
            'year' => 'required|integer',
            'month' => 'required|integer',
            'water' => 'required|integer',
            'warm' => 'nullable|integer'
        ];
        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        $pokaz = Pokaz::where('flat',$request->get('flat'))->where('year',$request->get('year'))->where('month',$request->get('month'))->get()->first();
        if ($pokaz == null){
            //сравнение с показаниями за предыдущий месяц
            $pokaz_prev = Pokaz::where('flat',$request->get('flat'))->where('year',Pokaz::getRepPeriodAdmin()['rep_year_prev'])->where('month',Pokaz::getRepPeriodAdmin()['rep_month_prev'])->get()->first();

            if ($pokaz_prev !== null){
                if ($pokaz_prev->water <= $request->get('water') && $pokaz_prev->warm <= $request->get('warm')){
                    $pokaz = new Pokaz();
                    $pokaz->flat = $request->get('flat');
                    $pokaz->year = $request->get('year');
                    $pokaz->month = $request->get('month');
                    $pokaz->water = $request->get('water');
                    $pokaz->warm = $request->get('warm');
                    $error_save = !$pokaz->save();
                    $error_message = '';
                } else {
                    $error_message = 'Вы вводите меньшие показания чем за прошлый месяц!';
                    $error_save = false;
                }
            } else {
                $pokaz = new Pokaz();
                $pokaz->flat = $request->get('flat');
                $pokaz->year = $request->get('year');
                $pokaz->month = $request->get('month');
                $pokaz->water = $request->get('water');
                $pokaz->warm = $request->get('warm');
                $error_save = !$pokaz->save();
                $error_message = '';
            }
        } else {
            $error_message = 'Показания для этой квартиры за указанный период уже переданы!';
            $error_save = false;
        }

        return view('admin.pokaz.create', [
            'error_message' => $error_message,
            'error_save' => $error_save,
            'flat' => $request->get('flat'),
            'rep_month' => $request->get('month'),
            'rep_year' => $request->get('year'),
        ]);
    }

    //формирование квитанции менеджером за квартиру
    public function adminCalcCreate()
    {

       // $result = $this->calcService->pull();

        return view('admin.calc.create', [
        ]);
    }
    public function adminCalc(Request $request)
    {
        $allowedFlatsList = implode(',',Flat::allowedFlats);
        $rules = [
            'flat' => "required|in:$allowedFlatsList",
        ];
        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        if (in_array($request->input('flat'),Flat::admin_flats)){
            echo 'Ошибка! Нельзя формировать квитанцию по этой квартире!';
            exit();
        }

        $periodParams = Pokaz::getRepPeriodAdmin();
        //dd($periodParams);

        //$period = Pokaz::getRepPeriod();
        //dd($period);

        $flat = Flat::where('number',$request->input('flat'))->first();
        if ($flat == null){
            echo "Ошибка! Не найдена квартира пользователя!";
            exit();
        }

        $pokaz = Pokaz::where('flat',$flat->number)->where('year',$periodParams['rep_year'])->where('month',$periodParams['rep_month'])->first();
        if ($pokaz == null){
            echo "Ошибка! Не занесены показания за текущий период!";
            exit();
        }
        $pokaz_prev = Pokaz::where('flat',$flat->number)->where('year',$periodParams['rep_year_prev'])->where('month',$periodParams['rep_month_prev'])->first();
        if ($pokaz_prev == null){
            echo "Ошибка! Не занесены показания за предыдущий период!";
            exit();
        }
        $tarif = Tarif::find(1);
        if ($tarif == null){
            echo "Ошибка! Не найдены тарифы!";
            exit();
        }


        $payment = Pokaz::getPayment($pokaz,$pokaz_prev,$tarif,$flat,$periodParams);


        $html = Pokaz::formatInvoice($payment);

        //сохраняем в кеш подготовленную html-строку для последующего скачивания в pdf
        Cache::put('generate_' . $flat->number, $html, Pokaz::REFRESH_TIME);
        /*
        return view('pokaz.calc', [
            'payment' => $payment,
        ]);
        */

        return view('admin.calc.calc', [
            'payment' => $payment,
        ]);
    }


    public function adminWarm()
    {
        //если по всем квартирам занесены показания и занесены текущие показания общедомового счетчика тепла
        $result = $this->calcService->pull();

        return view('admin.calc.warm', [
            'flatsWithoutPokaz_str' => count($result['flatsWithoutPokaz']) > 0? implode(',',$result['flatsWithoutPokaz']): '',
            'counter' => $result['counter'],
            'rep_month' => Pokaz::getMonthName($result['rep_month']),
            'rep_year' => $result['rep_year'],
        ]);
    }
}
