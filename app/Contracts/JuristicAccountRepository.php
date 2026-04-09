<?php

namespace App\Contracts;

use App\Http\Requests\CreateAccountRequest;

interface JuristicAccountRepository
{
    public function createAccount(CreateAccountRequest $accountRequest): bool;
}
