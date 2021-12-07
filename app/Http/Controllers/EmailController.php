<?php

namespace App\Http\Controllers;

use App\Models\Email;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    public function create()
    {
        if (!Auth::user()->is_manager || !Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        return view('admin.email.create', [
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
        try {
            $request->validate([
                'email' => 'required|string|email|max:255|unique:emails'
            ]);
        } catch (\ValidationException $exception) {
            return redirect('create');
        }

        /** @var Email $email */
        $email = new Email();
        $email->email = $request->get('email');

        $email->save();

        return redirect(route('email.create'));
    }
}
