<?php

namespace App\Http\Controllers;

use App\Models\Pokaz;
use App\Models\Tarif;
use App\Models\Flat;
use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon as Carbon;
use Illuminate\Support\Facades\Cache;


class PokazController extends Controller
{
    const REFRESH_TIME = 3600;
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
    public function create(Pokaz $pokaz)
    {
        $user = auth()->user();

        $periodParams = $pokaz->getPeriodParams();
        $year = $periodParams['year'];
        $month = $periodParams['month'];
        $year_prev = $periodParams['year_prev'];
        $month_prev = $periodParams['month_prev'];

        $pokaz = Pokaz::where('year',$year)->where('month',$month)->where('user_id',$user->id)->first();
        if ($pokaz !== null){
            $water = $pokaz->water;
            $warm = $pokaz->warm;
        } else {
            $water = '';
            $warm = '';
        }
        $pokaz_prev = Pokaz::where('year',$year_prev)->where('month',$month_prev)->where('user_id',$user->id)->first();
        $isPokazPrev = $pokaz_prev == null? false: true;

        return view('pokaz.create', [
            'user_id' => $user->id,
            'year' => $periodParams['year'],
            'month_m' => $periodParams['month_m'],
            'day' => $periodParams['day'],
            'year_prev' => $periodParams['year_prev'],
            'month_prev_m' => $periodParams['month_prev_m'],
            'day_prev' => $periodParams['day_prev'],
            'isPokazPrev' => $isPokazPrev,
            'water' => $water,
            'warm' => $warm,

        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Pokaz $pokaz)
    {
        $this->validate($request, [
            'water_prev' => 'integer',
            'warm_prev' => 'integer',
            'water' => 'required|integer',
            'warm' => 'required|integer'
        ]);

        $periodParams = $pokaz->getPeriodParams();

        //сохраняем показания за предыдущий месяц, если они были введены
        $water_prev = $request->get('water_prev');
        $warm_prev = $request->get('warm_prev');
        if ($water_prev !== null && $warm_prev !== null)
        {
            $pok = new Pokaz();
            $pok->user_id = Auth::user()->id;
            $pok->year = $periodParams['year_prev'];
            $pok->month = $periodParams['month_prev'];
            $pok->water = $water_prev;
            $pok->warm = $warm_prev;
            $pok->save();
            $pok->user()->associate(Auth::user());
        }

        //сохраняем показания за текущий месяц
        $pok = Pokaz::where('year',$periodParams['year'])->where('month',$periodParams['month'])->where('user_id',Auth::user()->id)->first();
        if ($pok == null) //если не найдены показания за текущий отчетный месяц
        {
            /** @var Pokaz $pokaz */
            $pok = new Pokaz();
            $pok->user_id = Auth::user()->id;
            $pok->year = $periodParams['year'];
            $pok->month = $periodParams['month'];
            $pok->water = $request->get('water');
            $pok->warm = $request->get('warm');
            $pok->save();
            $pok->user()->associate(Auth::user());
        }
        else //если найдены показания за текущий отчетный месяц, то обновляем их
        {
            $pok->water = $request->get('water');
            $pok->warm = $request->get('warm');
            $pok->save();
        }

        return redirect(route('pokaz.list', ['user_id' => Auth::user()->id]));
    }

    public function list($user_id)
    {
        $pokazs = Pokaz::where('user_id',$user_id)->get();

        return view('pokaz.list', [
             'pokazs' => $pokazs,
        ]);
    }

    public function listAll()
    {
        //$pokazs = Pokaz::where('user_id',$user_id)->get();

        return view('pokaz.listAll', [
            //'pokazs' => $pokazs,
        ]);
    }

    public function calc(Pokaz $pokaz)
    {
        $periodParams = $pokaz->getPeriodParams();
        $user_id = Auth::user()->id;


        $pokaz = Pokaz::where('user_id',$user_id)->where('year',$periodParams['year'])->where('month',$periodParams['month'])->first();
        $pokaz_prev = Pokaz::where('user_id',$user_id)->where('year',$periodParams['year_prev'])->where('month',$periodParams['month_prev'])->first();

        $tarif = Tarif::find(1);
        $flat = Flat::where('user_id',$user_id)->first();
        $data = Data::where('number',$flat->number)->first();

        $payment = [
            'day' => $periodParams['day'],
            'month' => $periodParams['month'],
            'month_m' =>  $periodParams['month_m'],
            'flat' => $flat->number,
            'fio' => $data->last_name . ' ' . mb_substr($data->name,0,1) . '.' . mb_substr($data->mid_name,0,1) . '.',
            'month_name' => Pokaz::getMonthName($periodParams['month']),
            'water_tarif' => $tarif->water,
            'service_tarif' => $tarif->service,
            'square_total' => $flat->square_total,
            'square_warm' => $flat->square_warm,
            'warmCounter' => $flat->warmCounter,
            'year' => $periodParams['year'],
            'water' => ($pokaz->water - $pokaz_prev->water) * $tarif->water,
            'warm' => $flat->warmCounter == true? number_format(round(($pokaz->warm - $pokaz_prev->warm) / 1163 * $tarif->warm * 1.1,2),2,'.',' ') : 3000,
            'warm_current' => number_format($pokaz->warm,0,'.',' '),
            'warm_previous' => number_format($pokaz_prev->warm,0,'.',' '),
            'service' => round($flat->square_total * $tarif->service,0),
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
        Cache::put($user_id, $html, self::REFRESH_TIME);

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
