<?php

namespace App\Http\Controllers;

use App\Models\Pull;
use Illuminate\Http\Request;

class PullController extends Controller
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
        return view('admin.pull.create', [

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
        try {
            $this->validate($request, [
                'flat' => 'required|string|unique:pulls',
            ]);
        } catch (\Exception $e){
            return back()->withErrors(['msg' => $e->getMessage()])->withInput();
        }

        $number = Pull::where('flat',$request->input('flat'))->get()->first();

        //var_dump($number);
        if ($number === null) {
            $error_save = !Pull::create(['flat' => $request->input('flat')]);
            $error_message = '';
            // return redirect(route('pull.create',['error_message' => $error_message,'flat' => $request->input('flat')]));
            return view('admin.pull.create', [

            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
