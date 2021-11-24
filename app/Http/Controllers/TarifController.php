<?php

namespace App\Http\Controllers;

use App\Models\Flat;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TarifController extends Controller
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
     * Display the specified resource.
     *
     * @param  \App\Models\Tarif  $tarif
     * @return \Illuminate\Http\Response
     */
    public function show(Tarif $tarif)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tarif  $tarif
     * @return \Illuminate\Http\Response
     */
    public function edit(Tarif $tarif)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403,'Доступ запрещен!');
        }
        $tarif = Tarif::find(1);

        return view('tarif.edit', [
                    'tarif' => $tarif
                            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tarif  $tarif
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tarif $tarif)
    {
        $rules = [
            'water' => 'required|numeric',
            'warm' => 'required|numeric',
            'service' => 'required|numeric',
            'lift' => 'required|numeric',
            'rubbish' => 'required|numeric',
            'parkingCleaning' => 'required|numeric',
            'parkingLightening' => 'required|numeric',
            'cons' => 'required|numeric',
        ];

        try {
            $validatedData = $request->validate($rules);
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Введены некорректные данные'])->withInput();
        }

        /** @var Tarif $tarif */
        $tarif = Tarif::find(1);
        if ($tarif == null) {
            $tarif = new Tarif();
        }
        $tarif->water = $request->get('water');
        $tarif->warm = $request->get('warm');
        $tarif->service = $request->get('service');
        $tarif->lift = $request->get('lift');
        $tarif->rubbish = $request->get('rubbish');
        $tarif->parkingCleaning = $request->get('parkingCleaning');
        $tarif->parkingLightening = $request->get('parkingLightening');
        $tarif->cons = $request->get('cons');

        $tarif->save();

        return view('tarif.update', [
                'tarif' => $tarif
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tarif  $tarif
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarif $tarif)
    {
        //
    }
}
