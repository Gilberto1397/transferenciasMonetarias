<?php

namespace App\Repositories;

use App\Contracts\FisicAccountRepository;
use App\Http\Requests\CreateAccountRequest;
use App\Models\FisicAccount;
use App\Models\User;

class FisicAccountRepositoryEloquent implements FisicAccountRepository
{
    public function createAccount(CreateAccountRequest $accountRequest, User $user): bool
    {
        $fisicAccount = new FisicAccount();
        $fisicAccount->fisicaccount_cpf = $accountRequest->cpf;
        $fisicAccount->fisicaccount_user = $user->id;
        $fisicAccount->fisicaccount_accounttype = 2;
        return $fisicAccount->save();
    }
}
