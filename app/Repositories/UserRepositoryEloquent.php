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

        if (!$createdUser instanceof User || empty($createdUser->id)) {
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

    public function getPayeeUserById(int $payeeId): User|null
    {
        $fisicSql = '
            select users.* from users
            inner join fisicaccounts on users.id = fisicaccounts.fisicaccount_user
            where users.id = :payeeId
        ';

        $juristicSql = '
            select users.* from users
            inner join juristicaccounts on users.id = juristicaccounts.juristicaccount_user
            where users.id = :payeeId
        ';

        $fisicResult = User::fromQuery($fisicSql, ['payeeId' => $payeeId]);
        $juristicResult = User::fromQuery($juristicSql, ['payeeId' => $payeeId]);

        if (($fisicResult->count() > 0 && $juristicResult->count() > 0) ||
            ($fisicResult->count() > 1 || $juristicResult->count() > 1)) {
            throw new \DomainException('Mais de uma conta encontrada para o destinatário!');
        }
        if ($fisicResult->count() === 0 && $juristicResult->count() === 0) {
            return null;
        }
        if ($fisicResult->count() === 1) {
            return $fisicResult->first();
        }
        return $juristicResult->first();
    }

    /**
     * @param User $payer
     * @param User $payee
     * @param float $value
     * @return bool
     * @throws \DomainException
     */
    public function transferValue(User $payer, User $payee, float $value): bool
    {
        $payer->balance -= $value;
        $payee->balance += $value;

        if (!$payer->save()) {
            throw new \DomainException('Falha ao debitar o valor da conta de origem!');
        }
        if (!$payee->save()) {
            throw new \DomainException('Falha ao creditar o valor da conta de destino!');
        }
        return true;
    }
}
