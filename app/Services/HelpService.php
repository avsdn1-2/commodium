<?php


namespace App\Services;


class HelpService
{
    public function setPreviousRoute()
    {
        session(['previous_route_' . auth()->user()->id => \Route::current()->getName()]);
    }

    public function getPreviousRoute($route):string
    {
        if (session()->has('previous_route_' . auth()->user()->id ) && session('previous_route_' . auth()->user()->id) == $route)
        {
            session()->forget('previous_route_' . auth()->user()->id);
            $message = 'Даные сохранены!';
        } else {
            $message = '';
        }
        return $message;
    }
}
