<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function index()
    {
        return view('error.index', []);
    }
    public function info_m_pokaz()
    {
        return view('error.info_m_pokaz', []);
    }
    public function info_m_kvit()
    {
        return view('error.info_m_kvit', []);
    }
}
