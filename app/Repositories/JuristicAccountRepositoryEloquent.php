<?php

namespace App\Repositories;

use App\Contracts\JuristicAccountRepository;
use App\Http\Requests\CreateAccountRequest;
use App\Models\JuristicAccount;
use App\Models\User;

class JuristicAccountRepositoryEloquent implements JuristicAccountRepository
{
    public function createAccount(CreateAccountRequest $accountRequest, User $user): bool
    {
        $juristicAccount = new JuristicAccount();
        $juristicAccount->cnpj = $accountRequest->cnpj;
        $juristicAccount->juristicaccount_user = $user->id;
        $juristicAccount->juristicaccount_accounttype = 1;
        return $juristicAccount->save();
    }
}
