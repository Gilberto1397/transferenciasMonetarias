<?php

namespace app\Contracts;

use App\Http\Requests\CreateAccountRequest;

interface UserRepository
{
    public function createUser(CreateAccountRequest $accountRequest): bool;
}
