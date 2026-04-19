<?php

namespace App\Contracts;

use App\Http\Requests\CreateAccountRequest;
use App\Models\User;

interface UserRepository
{
    public function createUser(CreateAccountRequest $accountRequest): User;

    /**
     * @param int $userId
     * @return User|null
     */
    public function getPayerUserById(int $userId): User|null;

    /**
     * @param int $payeeId
     * @return User|null
     */
    public function getPayeeUserById(int $payeeId): User|null;

    /**
     * @param User $payer
     * @param User $payee
     * @param float $value
     * @return bool
     * @throws \DomainException
     */
    public function transferValue(User $payer, User $payee, float $value): bool;
}
