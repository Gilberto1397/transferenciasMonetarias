<?php

namespace App\Providers;

use app\Contracts\FisicAccountRepository;
use app\Contracts\JuristicAccountRepository;
use app\Contracts\UserRepository;
use app\Repositories\JuristicAccountRepositoryEloquent;
use app\Repositories\UserRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public array $bindings = [
        UserRepository::class => UserRepositoryEloquent::class,
        JuristicAccountRepository::class => JuristicAccountRepositoryEloquent::class,
        FisicAccountRepository::class => JuristicAccountRepositoryEloquent::class
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
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
