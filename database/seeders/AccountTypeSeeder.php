<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {
        AccountType::create(
            [
                'accounttypes_id' => 1,
                'accounttypes_description' => 'Jurídica'
            ]
        );
        AccountType::create(
            [
                'accounttypes_id' => 2,
                'accounttypes_description' => 'Física'
            ]
        );
    }
}
