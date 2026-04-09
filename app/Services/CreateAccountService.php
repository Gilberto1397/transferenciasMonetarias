<?php

namespace App\Services;

use App\Contracts\FisicAccountRepository;
use App\Contracts\JuristicAccountRepository;
use App\Contracts\UserRepository;
use App\Http\Requests\CreateAccountRequest;
use App\Models\User;

class CreateAccountService
{
    private JuristicAccountRepository $accountRepository;
    private FisicAccountRepository $fisicAccountRepository;
    private UserRepository $userRepository;

    public function createAccount(CreateAccountRequest $request): void
    {
        $user = $this->userRepository->createUser($request);
    }

    private function chooseAccount(CreateAccountRequest $request, User $user): bool
    {
        if ($request->tipoConta === 1) {
            return $this->accountRepository->createAccount($request, $user);
        }
        return $this->fisicAccountRepository->createAccount($request, $user);
    }
}
