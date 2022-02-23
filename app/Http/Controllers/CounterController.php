<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Pokaz;
use App\Services\HelpService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounterController extends Controller
{
    private $helpService;

    public function __construct(HelpService $helpService)
    {
        $this->helpService = $helpService;
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
        if (!Auth::user()->is_manager && !Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $result = Pokaz::getRepPeriodAdmin();
        $message = $this->helpService->getPreviousRoute('counter.store');

        return view('admin.counter.create', [
            'message' => $message,
            'rep_month' => $result['rep_month'],
            'rep_year' => $result['rep_year'],
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
        if (!Auth::user()->is_manager || !Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $rules = [
            'year' => 'required|integer',
            'month' => 'required|integer',
            'warm' => 'required|numeric'
        ];
        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        //выбираем показания за последний имеющийся период
        $counter_prev = Counter::all()->sortByDesc('period')->first();
        if (is_null($counter_prev) || $counter_prev->warm <= $request->get('warm')){
            $counter = Counter::where('year',$request->get('year'))->where('month',$request->get('month'))->first();
            if (is_null($counter)){
                $counter = new Counter();
            }
            $counter->year = $request->get('year');
            $counter->month = $request->get('month');
            $counter->period = $request->get('year') . '-' . Pokaz::formatMonth($request->get('month')) . '-01';
            $counter->warm = $request->get('warm');
            $counter->user_id = auth()->user()->id;
            $counter->save();
            $this->helpService->setPreviousRoute();
        } else {
            return back()->withErrors(['msg' => 'Введены показания, меньшие чем за предыдущий месяц'])->withInput();
        }

        return redirect(route('counter.create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Counter  $counter
     * @return \Illuminate\Http\Response
     */
    public function show(Counter $counter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Counter  $counter
     * @return \Illuminate\Http\Response
     */
    public function edit(Counter $counter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Counter  $counter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Counter $counter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Counter  $counter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Counter $counter)
    {
        //
    }
}
