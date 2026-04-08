<?php

namespace app\Services;

use app\Contracts\JuristicAccountRepository;
use App\Http\Requests\CreateAccountRequest;

class CreateAccountService
{
    private JuristicAccountRepository $accountRepository;

    public function createAccount(CreateAccountRequest $request): void
    {

    }
}
