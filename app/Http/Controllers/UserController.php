<?php

namespace App\Http\Controllers;

use App\Models\Flat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $users = User::paginate(6);
        return view('admin.users.index',compact('users'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (!Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        return view('admin.users.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (!Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $allowedFlatsList = implode(',',Flat::allowedFlats);
        $rules = [
            'name' => 'required|string',
            'flat' => "required|string|in:$allowedFlatsList",
            'email' => 'required|string|email|max:255',
            'is_admin' => 'boolean|nullable',
            'is_manager' => 'boolean|nullable',
        ];

        try {
            $validatedData = $request->validate($rules);
        } catch (\ValidationException $exception) {
            return back()->withErrors(['msg' => $exception->getMessage()])->withInput();
        }

        $user->update([
            'name' => $request->get('name'),
            'flat' => $request->get('flat'),
            'email' => $request->get('email'),
            'is_admin' => !empty($request->get('is_admin')),
            'is_manager' => !empty($request->get('is_manager')),
        ]);
        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function delete(User $user)
    {
        if (!Auth::user()->is_admin ) {
            abort(403,'Доступ запрещен!');
        }
        $user->delete();
        return redirect()->route('user.index');
    }
}
