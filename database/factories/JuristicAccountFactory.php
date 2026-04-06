<?php

namespace Database\Factories;

use App\Models\JuristicAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class JuristicAccountFactory extends Factory
{
    protected $model = JuristicAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'juristicaccount_accounttype' => 1,
            'juristicaccount_cnpj' => $this->faker->unique()->cnpj(false),
        ];
    }
}
