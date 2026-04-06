<?php

namespace Database\Factories;

use App\Models\FisicAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class FisicAccountFactory extends Factory
{
    protected $model = FisicAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array{fisicaccount_accounttype:int, fisicaccount_cpf:string}
     */
    public function definition(): array
    {
        return [
            'fisicaccount_accounttype' => 2,

            // @phpstan-ignore-next-line
            'fisicaccount_cpf' => $this->faker->unique()->cpf(false),
        ];
    }
}
