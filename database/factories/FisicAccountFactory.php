<?php

namespace Database\Factories;

use App\Models\Clients;
use App\Models\FisicAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class FisicAccountFactory extends Factory
{
    protected $model = FisicAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'fisicaccount_accounttype' => 1,
            'fisicaccount_cpf' => $this->faker->unique()->cpf(false),
        ];
    }
}
