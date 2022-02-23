<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\Flat;
use App\Models\Pokaz;
use App\Models\Pull;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'flat' => 'required|string|max:5|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|confirmed|min:8',
            ]);
        } catch (\ValidationException $exception) {
            return redirect('register');
        }


        //Проверка на допустимость емейла и квартиры
        $allowedEmails = Email::getAllowedEmails();

        if (in_array($request->email,$allowedEmails) && in_array($request->flat,Flat::allowedFlats)){ //
            Auth::login($user = User::create([
                'name' => $request->name,
                'flat' => $request->flat,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]));

            event(new Registered($user));
            //return redirect(RouteServiceProvider::HOME);
            return redirect('/');
        } else {
            return back()->withErrors(['msg' => 'Недопустимая почта или квартира'])->withInput();
        }

    }
}
