<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class OnlyFisicAccounts implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = User::fromQuery('
            SELECT 1 FROM users
                     INNER JOIN juristicaccounts ON users.id = juristicaccounts.juristicaccount_user
                     WHERE id = :userId
        ', ['userId' => $value]);
        return $user->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A conta de origem deve ser do tipo física!';
    }
}
