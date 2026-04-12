<?php

namespace App\Contracts;

use App\Http\Requests\CreateAccountRequest;
use App\Models\User;

interface UserRepository
{
    public function createUser(CreateAccountRequest $accountRequest): User;

    /**
     * @param int $fisicAccountId
     * @return User|null
     */
    public function getUserByFisicAccountId(int $fisicAccountId): User|null;

    /**
     * @param int $accountId
     * @param int $accountType
     * @return User|null
     */
    public function getUserByAccountAndTypeId(int $accountId, int $accountType): User|null;

    /**
     * @param User $originAccount
     * @param User $destinationAccount
     * @param float $value
     * @return bool
     * @throws \DomainException
     */
    public function transferValue(User $originAccount, User $destinationAccount, float $value): bool;
}
