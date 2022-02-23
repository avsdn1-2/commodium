<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Flat;
use App\Models\Pull;
use App\Models\Tarif;
use App\Models\Tarifw;
use App\Models\User;
use App\Services\CalcService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pokaz;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private $calcService;

    public function __construct(CalcService $calcService)
    {
        $this->calcService = $calcService;
    }

    public function index()
    {
        if (!auth()->user()->is_admin && !auth()->user()->is_manager){
            abort(403,'Доступ запрещен!');
        }
        return view('admin.index');
    }

    public function adminCreate()
    {
        if (!Auth::user()->is_manager && !Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $result = Pokaz::getRepPeriodAdmin();

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
        $allowedFlatsList = implode(',',Flat::allowedFlats);
        $rules = [
            'flat' => "required|string|in:$allowedFlatsList",
            'year' => 'required|integer',
            'month' => 'required|integer',
            'water' => 'required|integer',
            'warm' => 'nullable|numeric'
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
                    $pokaz->savedBy = auth()->user()->id;
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
                $pokaz->savedBy = auth()->user()->id;
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
        if (!auth()->user()->is_admin && !auth()->user()->is_manager){
            abort(403,'Доступ запрещен!');
        }
        return view('admin.calc.create', [
        ]);
    }

    public function adminCalc(Request $request)
    {
        if (!auth()->user()->is_admin && !auth()->user()->is_manager){
            abort(403,'Доступ запрещен!');
        }

        $allowedFlatsList = implode(',',Flat::allowedFlats);
        $rules = [
            'flat' => "required|in:$allowedFlatsList",
        ];
        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        if (in_array($request->input('flat'),Flat::adminFlats)){
            echo 'Ошибка! Нельзя формировать квитанцию по этой квартире!';
            exit();
        }

        $payment = $this->calcService->getServiceData($request->input('flat'));

        return view('admin.calc.calc', [
            'payment' => $payment,
        ]);
    }

    public function adminWarm()
    {
        if (!auth()->user()->is_admin && !auth()->user()->is_manager){
            abort(403,'Доступ запрещен!');
        }

        //если по всем квартирам занесены показания и занесены текущие показания общедомового счетчика тепла
        $result = $this->calcService->pull();

        //сохраняем дополнительный тариф по теплу, если он рассчитан
        if ($result['tarifAdditional'] !== null){
            $tarifw = Tarifw::where('year',$result['rep_year'])->where('month',$result['rep_month'])->get()->first();
            if ($tarifw == null){
                Tarifw::create([
                    'year' => $result['rep_year'],
                    'month' => $result['rep_month'],
                    'tarifAdditional' => $result['tarifAdditional']
                ]);
            }
        }

        return view('admin.calc.warm', [
            'flatsWithoutPokaz_str' => count($result['flatsWithoutPokaz']) > 0? implode(',',$result['flatsWithoutPokaz']): '',
            'counter' => $result['counter'],
            'rep_month' => Pokaz::getMonthName($result['rep_month']),
            'rep_year' => $result['rep_year'],
        ]);
    }

    //формирование квитанции менеджером за тепло
    public function adminWcreate()
    {
        if (!auth()->user()->is_admin && !auth()->user()->is_manager){
            abort(403,'Доступ запрещен!');
        }

        $periodParams = Pokaz::getRepPeriodAdmin();
        $tarifw = Tarifw::where('year',$periodParams['rep_year'])->where('month',$periodParams['rep_month'])->get()->first();
        if ($tarifw == null){
            echo 'Не рассчитан дополнительный тариф по отоплению за отчетный период!';
            exit();
        }
        return view('admin.calc.wcreate');
    }

    public function adminWinvoice(Request $request)
    {
        if (!auth()->user()->is_admin && !auth()->user()->is_manager){
            abort(403,'Доступ запрещен!');
        }

        $allowedFlatsList = implode(',',Flat::allowedFlats);
        $rules = [
            'flat' => "required|in:$allowedFlatsList",
        ];
        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }
        $data = $this->calcService->getWarmData($request->input('flat'));

        return view('admin.calc.winvoice', [
                'data' => $data
        ]);
    }
}
