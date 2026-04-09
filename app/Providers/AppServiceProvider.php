<?php

namespace App\Providers;

use App\Contracts\FisicAccountRepository;
use App\Contracts\JuristicAccountRepository;
use App\Contracts\UserRepository;
use App\Repositories\JuristicAccountRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array<string, string>
     */
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
