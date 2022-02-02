<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PokazServiceInterface;
use App\Services\PokazService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(PokazServiceInterface::class,PokazService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
