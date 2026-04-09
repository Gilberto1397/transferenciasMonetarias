<?php

namespace App\Repositories;

use app\Contracts\UserRepository;
use App\Http\Requests\CreateAccountRequest;
use App\Models\User;

class UserRepositoryEloquent implements UserRepository
{

    public function createUser(CreateAccountRequest $accountRequest): User
    {
        $createdUser = User::create([
            'name' => $accountRequest->name,
            'email' => $accountRequest->email,
            'password' => bcrypt($accountRequest->password),
            'balance' => 0
        ]);

        if (! $createdUser instanceof User || empty($createdUser->id)) {
            throw new \DomainException('Não foi possível criar o titular da conta!');
        }
        return $createdUser;
    }
}
