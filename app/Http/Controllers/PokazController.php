<?php

namespace App\Http\Controllers;

use App\Mail\ErrorMessage;
use App\Models\Pokaz;
use App\Models\Tarif;
use App\Models\Flat;
use App\Services\HelpService;
use App\Services\PokazServiceInterface;
use App\Services\PokazService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon as Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Throwable;


class PokazController extends Controller
{
    private $helpService;
    private $pokazService;
    //private $middleware;

    public function __construct(HelpService $helpService,PokazService $pokazService) //
    {
        $this->helpService = $helpService;
        $this->pokazService = $pokazService;
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
        if (in_array($user->flat,['admin','admin1'])){
            return redirect(route('error.info_m_pokaz'));
        }

        $period = Pokaz::getRepPeriod();
        $pokaz = $this->pokazService->getPokaz($user,$period);
        $message = $this->helpService->getPreviousRoute('pokaz.store');

        return view('pokaz.create', [
            'flat' => $user->flat,
            'day' => $period['day'],
            'rep_year' => $period['rep_year'],
            'rep_month' => $period['rep_month'],
            'rep_month_prev' => $period['rep_month_prev'],
            'rep_year_prev' => $period['rep_year_prev'],
            'start_pokaz_period' => Pokaz::START_POKAZ_PERIOD,
            'end_pokaz_period' => Pokaz::END_POKAZ_PERIOD,
            'warm' => $pokaz['warm'],
            'water' => $pokaz['water'],
            'warm_prev' => $pokaz['warm_prev'],
            'water_prev' => $pokaz['water_prev'],
            'message' => $message,
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
            'warm_prev' => 'nullable|integer'
        ]);
        $user = auth()->user();
        $period = Pokaz::getRepPeriod();

        $error_message = $this->pokazService->savePokaz(
            $user,
            $period,
            $request->get('water_prev'),
            $request->get('warm_prev'),
            $request->get('water'),
            $request->get('warm')
        );

        if ($error_message == "") {
            $this->helpService->setPreviousRoute();
            return redirect(route('pokaz.create'));
        } else {
            return back()->withErrors(['msg' => $error_message])->withInput();
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

        try {
            $data = Pokaz::getData($request->get('volume'),$request->get('year'),$request->get('month'));
        } catch (Throwable $e){
            Mail::to(Pokaz::admin_email)->send(new ErrorMessage(auth()->user()->email . '|' . $e->getFile() . '|' . $e->getLine() . '|' . $e->getMessage()  ));
            return redirect(route('error.index'));
        }

        return view('pokaz.list', [
            'pokazs' => $data['pokazs'],
            'prev' => $data['prev'],
            'total' => $data['total'],
            'counter' => $data['counter'],
            'counter_prev' => $data['counter_prev'],
        //    'rep_month' => $result['rep_month'],
        //    'rep_year' => $result['rep_year'],
            'rep_month' => $request->get('month'),
            'rep_year' => $request->get('year'),
            'volume' => $request->get('volume'),
        ]);
    }

    public function calc()
    {
        $user = auth()->user();
        if (in_array($user->flat,['admin','admin1'])){
            return redirect(route('error.info_m_kvit'));
        }

        $periodParams = Pokaz::getRepPeriod();
        $pokaz = Pokaz::where('flat',Auth::user()->flat)->where('year',$periodParams['rep_year'])->where('month',$periodParams['rep_month'])->first();
        if ($pokaz == null){
            echo "Ошибка! Не занесены показания за текущий период!";
            exit();
        }
        $pokaz_prev = Pokaz::where('flat',Auth::user()->flat)->where('year',$periodParams['rep_year_prev'])->where('month',$periodParams['rep_month_prev'])->first();
        if ($pokaz_prev == null){
            echo "Ошибка! Не занесены показания за предыдущий период!";
            exit();
        }
        $tarif = Tarif::find(1);
        if ($tarif == null){
            echo "Ошибка! Не найдены тарифы!";
            exit();
        }
        $flat = Flat::where('number',Auth::user()->flat)->first();

        if ($flat == null){
            echo "Ошибка! Не найдена квартира пользователя!";
            exit();
        }

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
