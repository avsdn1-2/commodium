<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;
    protected $fillable = [
        'year',
        'month',
        'value'
    ];
    public static function is_previous_route(string $routeName) : bool
    {
        $previousRequest = app('request')->create(\URL::previous());

        try {
            $previousRouteName = app('router')->getRoutes()->match($previousRequest)->getName();
            var_dump($previousRouteName);

        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
            // Exception is thrown if no mathing route found.
            // This will happen for example when comming from outside of this app.
            return false;
        }

        return $previousRouteName === $routeName;
    }
}
