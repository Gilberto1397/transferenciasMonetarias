<?php

namespace App\Contracts;

use App\Http\Requests\CreateAccountRequest;

interface FisicAccountRepository
{
    public function createAccount(CreateAccountRequest $accountRequest): bool;
}
