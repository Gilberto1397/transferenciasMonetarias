<?php

namespace Database\Seeders;

use App\Models\FisicAccount;
use App\Models\JuristicAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        AccountTypeSeeder::run();
        User::factory(10)->juristicAccount()->has(JuristicAccount::factory(1), 'juristicAccount')->create();
        User::factory(10)->fisicAccount()->has(FisicAccount::factory(1), 'fisicAccount')->create();
    }
}
