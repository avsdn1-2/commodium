<?php

namespace App\Http\Controllers;

use App\Mail\ErrorMessage;
use App\Models\Pokaz;
use App\Models\Tarif;
use App\Models\Flat;
use App\Services\CalcService;
use App\Services\HelpService;
use App\Services\PokazServiceInterface;
use App\Services\PokazService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon as Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;


class PokazController extends Controller
{
    private $helpService;
    private $pokazService;
    private $calcService;
    //private $middleware;

    public function __construct(HelpService $helpService,PokazService $pokazService,CalcService $calcService) //
    {
        $this->helpService = $helpService;
        $this->pokazService = $pokazService;
        $this->calcService = $calcService;
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //показ формы для заведения показаний
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
    //сохранение введенных показаний
    public function store(Request $request)
    {
        $this->validate($request, [
            'water' => 'required|integer',
            'warm' => 'nullable|numeric',
            'water_prev' => 'integer',
            'warm_prev' => 'nullable|numeric'
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

    //показ формы для формирования статистики
    public function list()
    {
        $result = Pokaz::getRepPeriodAdmin();

        return view('pokaz.list_gcal', [
            'rep_month' => $result['rep_month'],
            'rep_year' => $result['rep_year'],
            'volume' => '',
            'units' => '',
        ]);
    }

    //отображение запрошенной статистики показаний
    public function info(Request $request)
    {
        $allowedVolumeList = implode(',',['my','all']);
        $allowedUnitsList = implode(',',['gcal','raw']);
        $rules = [
            'year' => 'required|integer',
            'month' => 'required|integer',
            'volume' => "required|in:$allowedVolumeList",
            'units' => "required|in:$allowedUnitsList",
        ];
        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        if ($request->get('units') === 'gcal'){
            try {
                $data = Pokaz::getDataInGcal($request->get('volume'),$request->get('year'),$request->get('month'));
            } catch (Throwable $e){
                Mail::to(Pokaz::admin_email)->send(new ErrorMessage(auth()->user()->email . '|' . $e->getFile() . '|' . $e->getLine() . '|' . $e->getMessage()  ));
                return redirect(route('error.index'));
            }

            return view('pokaz.list_gcal', [
                'pokazs' => $data['pokazs'],
                'prev' => $data['prev'],
                'total' => $data['total'],
                'counter' => $data['counter'],
                'counter_prev' => $data['counter_prev'],
                'rep_month' => $request->get('month'),
                'rep_year' => $request->get('year'),
                'volume' => $request->get('volume'),
                'units' => $request->get('units'),
            ]);
        } elseif ($request->get('units') === 'raw'){
            try {
                $data = Pokaz::getDataInRaw($request->get('volume'),$request->get('year'),$request->get('month'));
            } catch (Throwable $e){
                Mail::to(Pokaz::admin_email)->send(new ErrorMessage(auth()->user()->email . '|' . $e->getFile() . '|' . $e->getLine() . '|' . $e->getMessage()  ));
                return redirect(route('error.index'));
            }

            return view('pokaz.list_raw', [
                'pokazs' => $data['pokazs'],
                'prev' => $data['prev'],
                'pokaz_units' => $data['units'],
                'rep_month' => $request->get('month'),
                'rep_year' => $request->get('year'),
                'volume' => $request->get('volume'),
                'units' => $request->get('units'),
            ]);

        }

    }

    //формирование квитанции пользователем по своей квартире
    public function calc()
    {
        $user = auth()->user();
        if (in_array($user->flat,['admin','admin1'])){
            return redirect(route('error.info_m_kvit'));
        }

        $payment = $this->calcService->getServiceData(Auth::user()->flat);

        if(count($payment) == 0){
           return  redirect(route('error.no_pokaz'));
        }

        return view('pokaz.calc', [
            'payment' => $payment,
        ]);
    }


}
