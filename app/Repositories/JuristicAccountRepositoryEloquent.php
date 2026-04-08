<?php

namespace app\Repositories;

use app\Contracts\JuristicAccountRepository;
use App\Http\Requests\CreateAccountRequest;

class JuristicAccountRepositoryEloquent implements JuristicAccountRepository
{
    public function createAccount(CreateAccountRequest $accountRequest): bool
    {

    }
}
