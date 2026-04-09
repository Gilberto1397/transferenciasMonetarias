<?php

namespace App\Contracts;

use App\Http\Requests\CreateAccountRequest;
use App\Models\User;

interface JuristicAccountRepository
{
    public function createAccount(CreateAccountRequest $accountRequest, User $user): bool;
}
