<?php

namespace App\Http\Controllers;

use App\Models\Pokaz;
use App\Models\Tarif;
use App\Models\Flat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon as Carbon;
use Illuminate\Support\Facades\Cache;

class PokazController extends Controller
{
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

        $pokaz = Pokaz::where('user_id',$user->id)->where('year',$rep_year)->where('month',$rep_month)->get()->first();
        if ($pokaz !== null){
            $warm = $pokaz->warm;
            $water = $pokaz->water;
        } else {
            $warm = null;
            $water = null;
        }
        $pokaz_prev = Pokaz::where('user_id',$user->id)->where('year',$rep_year_prev)->where('month',$rep_month_prev)->get()->first();
        if ($pokaz_prev !== null){
            $warm_prev = $pokaz_prev->warm;
            $water_prev = $pokaz_prev->water;
        } else {
            $warm_prev = 0;
            $water_prev = 0;
        }

        return view('pokaz.create', [
            'user_id' => $user->id,
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
        $day = $result['day'];
        $rep_month = $result['rep_month'];
        $rep_year = $result['rep_year'];
        $rep_month_prev = $result['rep_month_prev'];
        $rep_year_prev = $result['rep_year_prev'];

        //если введены предыдущие показания, то сохраняем их
        if ($request->get('water_prev') > 0 && $request->get('warm_prev') > 0){
            $pokaz_prev = new Pokaz();
            $pokaz_prev->user_id = Auth::user()->id;
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
        $pokaz = Pokaz::where('user_id',$user->id)->where('year',$rep_year)->where('month',$rep_month)->get()->first();

        //если нет введенных показаний за отчетный период, то сохраняем их
        if ($pokaz == null){
            $pokaz = new Pokaz();
            $pokaz->user_id = Auth::user()->id;
            $pokaz->year = $rep_year;
            $pokaz->month = $rep_month;
            $pokaz->water = $request->get('water');
            $pokaz->warm = $request->get('warm');
            $pokaz->save();
        } else { //если есть введенные показания за отчетный период, то сохраняем, если введены большие показания; если введены меньшие, то показываем ошибку
            if ($request->get('water') >= $pokaz->water && $request->get('warm') >= $pokaz->warm){
                $pokaz->water = $request->get('water');
                $pokaz->warm = $request->get('warm');
                $pokaz->save();
            } else {
                $error_message = "Вы вводите меньшие показания по сравнению с введенными ранее!";
                return view('pokaz.create', [
                    'error_message' => $error_message,
                    'user_id' => $user->id,
                    'rep_year' => $rep_year,
                    'rep_month' => $rep_month,
                    'day' => $day,
                    'start_pokaz_period' => Pokaz::START_POKAZ_PERIOD,
                    'end_pokaz_period' => Pokaz::END_POKAZ_PERIOD,
                    'warm' => $pokaz->warm,
                    'water' => $pokaz->water,
                   // 'warm_prev' => $warm_prev,
                   // 'water_prev' => $water_prev,
                ]);
            }
        }

        return redirect(route('pokaz.list', ['user_id' => Auth::user()->id]));
    }

    public function list($user_id)
    {
        $pokazs = Pokaz::where('user_id',$user_id)->orderBy('id','desc')->get();

        return view('pokaz.list', [
             'pokazs' => $pokazs,
        ]);
    }

    public function calc()
    {
        /*
        $date = date('Y-m-d',time());
        $year = date('Y',strtotime($date));
        $month = date('n',strtotime($date));
        $date_prev = Carbon::createFromFormat('Y-m-d', $date)->subMonth()->format('Y-m-d');
        $year_prev = date('Y',strtotime($date_prev));
        $month_prev = date('n',strtotime($date_prev));
        $user_id = Auth::user()->id;
        //dd($user_id);
        */
        /*
        var_dump($year);
        var_dump($month);
        var_dump($year_prev);
        var_dump($month_prev);
        */
        $user_id = Auth::user()->id;
        $user_flat = Auth::user()->flat;

        $periodParams = Pokaz::getRepPeriod();
        /*
        $day = $result['day'];
        $rep_month = $result['rep_month'];
        $rep_year = $result['rep_year'];
        $rep_month_prev = $result['rep_month_prev'];
        $rep_year_prev = $result['rep_year_prev'];
        */

        $pokaz = Pokaz::where('user_id',$user_id)->where('year',$periodParams['rep_year'])->where('month',$periodParams['rep_month'])->first();
        //dd($pokaz);
        $pokaz_prev = Pokaz::where('user_id',$user_id)->where('year',$periodParams['rep_year_prev'])->where('month',$periodParams['rep_month_prev'])->first();
        $tarif = Tarif::find(1);
        $flat = Flat::where('number',$user_flat)->first();
        $payment = [
            'day' => $periodParams['day'],
            'month' => $periodParams['rep_month'],
            'month_m' =>  $periodParams['rep_month_m'],
            'flat' => $flat->number,
            'fio' => $flat->name . ' ' . mb_substr($flat->first_name,0,1) . '.' . mb_substr($flat->mid_name,0,1) . '.',
            'month_name' => Pokaz::getMonthName($periodParams['rep_month']),
            'water_tarif' => $tarif->water,
            'service_tarif' => $tarif->service,
            'square' => $flat->square,
            'warmCounter' => $flat->warmCounter,
            'year' => $periodParams['rep_year'],
            'water' => ($pokaz->water - $pokaz_prev->water) * $tarif->water,
            'warm' => $flat->warmCounter == true? number_format(round(($pokaz->warm - $pokaz_prev->warm) / 1163.06 * $tarif->warm * 1.1,2),2,'.',' ') : 3000,
            'warm_current' => number_format($pokaz->warm,0,'.',' '),
            'warm_previous' => number_format($pokaz_prev->warm,0,'.',' '),
            'service' => round($flat->square * $tarif->service,0),
            'lift' => $tarif->lift,
            'rubbish' => $tarif->rubbish,
            'parkingCleaning' => $tarif->parkingCleaning,
            'parkingLightening' => $tarif->parkingLightening,
            'cons' => $tarif->cons
        ];
        $payment['total'] = $payment['service'] + $payment['lift'] + $payment['rubbish'] + $payment['water'] +
            $payment['parkingCleaning'] + $payment['parkingLightening'];


        $html = Pokaz::formatInvoice($payment);

        //сохраняем в кеш подготовленную html-строку для последующего скачивания в pdf
        Cache::put($user_id, $html, Pokaz::REFRESH_TIME);

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
