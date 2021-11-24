<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    //
    public function create()
    {


        return view('email.create', [

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
            $request->validate([
                'email' => 'required|string|email|max:255|unique:emails'
            ]);
        } catch (\ValidationException $exception) {
            //dd($exception->getMessage());
            return redirect('create');
        }

        /** @var Email $email */
        $email = new Email();
        $email->email = $request->get('email');

        $email->save();

        return redirect(route('email.create'));
    }
}
