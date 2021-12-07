<?php

namespace App\Http\Controllers;

use App\Models\Flat;
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
        //
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
        $rules = [
            'number' => 'required|string|unique:flats',
            'square' => 'required|numeric',
            'privilege' => 'nullable|numeric',
            'name' => 'required|string',
            'first_name' => 'required|string',
            'mid_name' => 'required|string',
        ];

        try {
                $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            //return back()->withErrors(['msg' => 'Введены некорректные данные'])->withInput();
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        /** @var Flat $flat */
        $flat = new Flat();
        $flat->number = $request->get('number');
        $flat->square = $request->get('square');
        $flat->warmCounter = empty($request->get('warmCounter'))? false: true;
        $flat->useLift = empty($request->get('useLift'))? false: true;
        $flat->privilege = empty($request->get('privilege'))? 0: $request->get('privilege');
        $flat->name = $request->get('name');
        $flat->first_name = $request->get('first_name');
        $flat->mid_name = $request->get('mid_name');

        $flat->save();
        $flat->user()->associate(Auth::user());

        //return redirect(route('home'));
        return view('admin.flat.update');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Flat  $flat
     * @return \Illuminate\Http\Response
     */
    public function show(Flat $flat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Flat  $flat
     * @return \Illuminate\Http\Response
     */
    public function edit(Flat $flat)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Flat  $flat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Flat $flat)
    {
        //
    }
}
