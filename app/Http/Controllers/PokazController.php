<?php

namespace App\Http\Controllers;

use App\Models\Pokaz;
use App\Models\Tarif;
use App\Models\Flat;
use App\Services\HelpService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon as Carbon;
use Illuminate\Support\Facades\Cache;

class PokazController extends Controller
{
    private $helpService;
    //private $middleware;

    public function __construct(HelpService $helpService)
    {
        $this->helpService = $helpService;
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {



        $user = auth()->user();
        $result = Pokaz::getRepPeriod();

        $day = $result['day'];
        $rep_month = $result['rep_month'];
        $rep_year = $result['rep_year'];
        $rep_month_prev = $result['rep_month_prev'];
        $rep_year_prev = $result['rep_year_prev'];

        $pokaz = Pokaz::where('flat',$user->flat)->where('year',$rep_year)->where('month',$rep_month)->get()->first();
        if ($pokaz !== null){
            $warm = $pokaz->warm;
            $water = $pokaz->water;
        } else {
            $warm = null;
            $water = null;
        }
        $pokaz_prev = Pokaz::where('flat',$user->flat)->where('year',$rep_year_prev)->where('month',$rep_month_prev)->get()->first();
        if ($pokaz_prev !== null){
            $warm_prev = $pokaz_prev->warm;
            $water_prev = $pokaz_prev->water;
        } else {
            $warm_prev = 0;
            $water_prev = 0;
        }

        return view('pokaz.create', [
            'flat' => $user->flat,
            'rep_year' => $rep_year,
            'rep_month' => $rep_month,
            'rep_month_prev' => $rep_month_prev,
            'rep_year_prev' => $rep_year_prev,
            'day' => $day,
            'start_pokaz_period' => Pokaz::START_POKAZ_PERIOD,
            'end_pokaz_period' => Pokaz::END_POKAZ_PERIOD,
            'warm' => $warm,
            'water' => $water,
            'warm_prev' => $warm_prev,
            'water_prev' => $water_prev,
        ]);


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'water' => 'required|integer',
            'warm' => 'nullable|integer',
            'water_prev' => 'integer',
            'warm_prev' => 'integer'
        ]);
        $user = auth()->user();
        $result = Pokaz::getRepPeriod();
        //dd($result);
        $day = $result['day'];
        $rep_month = $result['rep_month'];
        $rep_year = $result['rep_year'];
        $rep_month_prev = $result['rep_month_prev'];
        $rep_year_prev = $result['rep_year_prev'];

        $error_message = "";
        //если введены предыдущие показания, то сохраняем их
        if ($request->get('water_prev') > 0 && $request->get('warm_prev') > 0){
            $pokaz_prev = new Pokaz();
            $pokaz_prev->flat = $user->flat;
            $pokaz_prev->year = $rep_year_prev;
            $pokaz_prev->month = $rep_month_prev;
            $pokaz_prev->water = $request->get('water_prev');
            $pokaz_prev->warm = $request->get('warm_prev');
            $pokaz_prev->save();
            $water_prev = $request->get('water_prev');
            $warm_prev = $request->get('warm_prev');
        } else {
            //$water_prev = 0;
            //$warm_prev = 0;
        }

        //проверяем, есть ли введенные показания за отчетный период
        $pokaz = Pokaz::where('flat',$user->flat)->where('year',$rep_year)->where('month',$rep_month)->get()->first();

        //если нет введенных показаний за отчетный период, то сохраняем их
        if ($pokaz == null){

            //проверяем, есть ли показания за предыдущий период
            $pokaz_prev = Pokaz::where('flat',$user->flat)->where('year',$rep_year_prev)->where('month',$rep_month_prev)->get()->first();
            if ($pokaz_prev->water <= $request->get('water') && $pokaz_prev->warm <= $request->get('warm')){

                $pokaz = new Pokaz();
                $pokaz->flat = $user->flat;
                $pokaz->year = $rep_year;
                $pokaz->month = $rep_month;
                $pokaz->water = $request->get('water');
                $pokaz->warm = $request->get('warm');
                $pokaz->save();
            } else {
                $error_message = "Вы вводите меньшие показания чем за предыдущий период!";
            }
        } else { //если есть введенные показания за отчетный период, то сохраняем, если введены большие показания; если введены меньшие, то показываем ошибку
            if ($request->get('water') >= $pokaz->water && $request->get('warm') >= $pokaz->warm){
                $pokaz->water = $request->get('water');
                $pokaz->warm = $request->get('warm');
                $pokaz->save();
            } else {
                $error_message = "Вы вводите меньшие показания по сравнению с введенными ранее!";
            }
        }
        if ($error_message == "") {
            return redirect(route('pokaz.list', ['flat' => Auth::user()->flat]));
        } else {
            return view('pokaz.create', [
                'error_message' => $error_message,
                'rep_year' => $rep_year,
                'rep_month' => $rep_month,
                'day' => $day,
                'start_pokaz_period' => Pokaz::START_POKAZ_PERIOD,
                'end_pokaz_period' => Pokaz::END_POKAZ_PERIOD,
                'warm' => $request->get('warm'),
                'water' => $request->get('water'),
            ]);
        }

    }


    public function list()
    {
        $result = Pokaz::getRepPeriodAdmin();

        return view('pokaz.list', [
          //  'pokazs' => $pokazs,
            'rep_month' => $result['rep_month'],
            'rep_year' => $result['rep_year'],
            'volume' => '',
        ]);
    }

    public function info(Request $request)
    {
        $rules = [
            'year' => 'required|integer',
            'month' => 'required|integer',
           // 'volume' => 'required|in(["my","all"])',
        ];
        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        $result = Pokaz::getRepPeriodAdmin();

        $data = Pokaz::getData($request->get('volume'),$request->get('year'),$request->get('month'));
        //dd($data);

        return view('pokaz.list', [
            'pokazs' => $data['pokazs'],
            'prev' => $data['prev'],
            'total' => $data['total'],
            'counter' => $data['counter'],
            'counter_prev' => $data['counter_prev'],
            'rep_month' => $result['rep_month'],
            'rep_year' => $result['rep_year'],
            'volume' => $request->get('volume'),
        ]);
    }

    public function calc()
    {
        $periodParams = Pokaz::getRepPeriod();
        $pokaz = Pokaz::where('flat',Auth::user()->flat)->where('year',$periodParams['rep_year'])->where('month',$periodParams['rep_month'])->first();
        $pokaz_prev = Pokaz::where('flat',Auth::user()->flat)->where('year',$periodParams['rep_year_prev'])->where('month',$periodParams['rep_month_prev'])->first();
        $tarif = Tarif::find(1);
        $flat = Flat::where('number',Auth::user()->flat)->first();

        $payment = Pokaz::getPayment($pokaz,$pokaz_prev,$tarif,$flat,$periodParams);

        $html = Pokaz::formatInvoice($payment);

        //сохраняем в кеш подготовленную html-строку для последующего скачивания в pdf
        Cache::put(Auth::user()->id, $html, Pokaz::REFRESH_TIME);

        return view('pokaz.calc', [
            'payment' => $payment,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pokaz  $pokaz
     * @return \Illuminate\Http\Response
     */
    public function show(Pokaz $pokaz)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pokaz  $pokaz
     * @return \Illuminate\Http\Response
     */
    public function edit(Pokaz $pokaz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pokaz  $pokaz
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pokaz $pokaz)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pokaz  $pokaz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pokaz $pokaz)
    {
        //
    }
}
