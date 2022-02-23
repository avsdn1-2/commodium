<?php

namespace App\Http\Controllers;

use App\Models\Flat;
use App\Models\Pokaz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\ValidationException;


class FlatController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->is_admin && !Auth::user()->is_manager) {
            abort(403,'Доступ запрещен!');
        }
        $flats = Flat::paginate(6);
        return view('admin.flat.index',compact('flats'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        return view('admin.flat.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $allowedFlatsList = implode(',',Flat::allowedFlats);
        $allowedCounterTypeList = implode(',',['1','2','3','4']);
        $rules = [
            'number' => "required|string|unique:flats|in:$allowedFlatsList",
            'warmCounter' => 'boolean|nullable',
            'counterType' => "required|in:$allowedCounterTypeList",
            'useLift' => 'boolean|nullable',
            'square_total' => 'required|numeric',
            'square_warm' => 'required|numeric',
            'residents' => 'required|integer',
            'privilege' => 'nullable|numeric',
            'name' => 'required|string',
            'first_name' => 'required|string',
            'mid_name' => 'required|string',
        ];

        try {
                $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        /** @var Flat $flat */
        $flat = new Flat();
        $flat->number = $request->get('number');
        $flat->square_total = $request->get('square_total');
        $flat->square_warm = $request->get('square_warm');
        $flat->residents = $request->get('residents');
        //$flat->warmCounter = !empty($request->get('warmCounter'));
        $flat->warmCounter = true;
        $flat->counterType = $request->get('counterType');
        $flat->useLift = !empty($request->get('useLift'));
        //$flat->privilege = empty($request->get('privilege'))? 0: $request->get('privilege');
        $flat->privilege = 0;
        $flat->name = $request->get('name');
        $flat->first_name = $request->get('first_name');
        $flat->mid_name = $request->get('mid_name');

        $flat->save();

        return view('admin.flat.update');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Flat  $flat
     * @return \Illuminate\Http\Response
     */
    public function edit(Flat $flat)
    {
        if (!Auth::user()->is_admin && !Auth::user()->is_manager) {
            abort(403,'Доступ запрещен!');
        }
        return view('admin.flat.edit',compact('flat'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Flat  $flat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Flat $flat)
    {
        if (!Auth::user()->is_admin && !Auth::user()->is_manager) {
            abort(403,'Доступ запрещен!');
        }
        $allowedFlatsList = implode(',',Flat::allowedFlats);
        $allowedCounterTypeList = implode(',',['1','2','3','4']);
        $rules = [
            'number' => "required|string|in:$allowedFlatsList",
            'counterType' => "required|in:$allowedCounterTypeList",
            'useLift' => 'boolean|nullable',
            'square_total' => 'required|numeric',
            'square_warm' => 'required|numeric',
            'residents' => 'required|integer',
            'name' => 'required|string',
            'first_name' => 'required|string',
            'mid_name' => 'required|string',
        ];

        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }
        $flat->update([
            'number' => $request->get('number'),
            'square_total' =>  $request->get('square_total'),
            'square_warm' =>  $request->get('square_warm'),
            'residents' => $request->get('residents'),
            'counterType' => $request->get('counterType'),
            'useLift' => !empty($request->get('useLift')),
            'name' => $request->get('name'),
            'first_name' => $request->get('first_name'),
            'mid_name' => $request->get('mid_name'),
        ]);
        return redirect(route('flat.index'));
    }


}
