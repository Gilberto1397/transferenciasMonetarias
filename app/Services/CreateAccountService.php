<?php

namespace App\Services;

use app\Contracts\FisicAccountRepository;
use app\Contracts\JuristicAccountRepository;
use app\Contracts\UserRepository;
use App\Http\Requests\CreateAccountRequest;

class CreateAccountService
{
    private JuristicAccountRepository $accountRepository;
    private FisicAccountRepository $fisicAccountRepository;
    private UserRepository $userRepository;

    public function createAccount(CreateAccountRequest $request): void
    {
        $user = $this->userRepository->createUser($request);
    }

    private function chooseAccountCreate()
    {

    }
}
