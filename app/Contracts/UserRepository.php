<?php

namespace App\Contracts;

use App\Http\Requests\CreateAccountRequest;
use App\Models\User;

interface UserRepository
{
    public function createUser(CreateAccountRequest $accountRequest): User;
}
