<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function index()
    {
        $message = 'привет';

        // Без "use"
            $example = function () {
                global $message;
                var_dump($message);
                echo '<br>';
            };
            $example();

        // Наследуем $message
            $example = function () use ($message) {
                var_dump($message);
                echo '<br>';
            };
            $example();

            // Наследование по ссылке
            $example = function () use (&$message) {
                var_dump($message);
            };
            $example();
    }
}
