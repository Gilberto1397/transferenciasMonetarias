<?php

namespace App\Repositories;

use App\Contracts\UserRepository;
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

    public function getPayerUserById(int $userId): User|null
    {
        return User::fromQuery('
            select users.* from users
            inner join fisicaccounts on users.id = fisicaccounts.fisicaccount_user
            where users.id = :userId
                       ',
            ['userId' => $userId]
        )->first();
    }

    public function getUserByAccountAndTypeId(int $accountId, int $accountType): User|null
    {
        $sql = 'select users.* from users';

        if ($accountType === 2) {
            $sql .= ' inner join fisicaccounts on users.id = fisicaccounts.fisicaccount_user
                      where fisicaccounts.fisicaccount_id = :accountId';
        } else {
            $sql .= ' inner join juristicaccounts on users.id = juristicaccounts.juristicaccount_user
                      where juristicaccounts.juristicaccount_id = :accountId';
        }
        return User::fromQuery($sql, ['accountId' => $accountId])->first();
    }

    /**
     * @param User $originAccount
     * @param User $destinationAccount
     * @param float $value
     * @return bool
     * @throws \DomainException
     */
    public function transferValue(User $originAccount, User $destinationAccount, float $value): bool
    {
        $originAccount->balance -= $value;
        $destinationAccount->balance += $value;

        if (! $originAccount->save()) {
            throw new \DomainException('Falha ao debitar o valor da conta de origem!');
        }
        if (! $destinationAccount->save()) {
            throw new \DomainException('Falha ao creditar o valor da conta de destino!');
        }
        return true;
    }
}
