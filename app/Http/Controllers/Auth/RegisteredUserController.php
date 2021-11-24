<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Email;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    protected $allowedFlats = ['1','2','3','4','5','6','7','8','9','10','11','12','12а','14','15','16','17',
        '18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37',
        '38','39','40','41','42','43','44','45','46','47','48','49','50','51','52','53','54'];
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
        //var_dump($request->email);
        //var_dump(in_array($request->email,$this->allowedFlats));
        //exit();
        if (in_array($request->email,$allowedEmails) && in_array($request->flat,$this->allowedFlats)){ //
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
