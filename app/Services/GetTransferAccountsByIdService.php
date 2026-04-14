<?php

namespace App\Services;

use App\Contracts\UserRepository;
use App\Http\Requests\TransferRequest;
use App\Models\User;

class GetTransferAccountsByIdService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param TransferRequest $request
     * @return array<string, User>
     */
    public function getTransferAccountsById(TransferRequest $request)
    {
        $originAccount = $this->getOriginAccount($request->originId);
        $destinationAccount = $this->getDestinationAccount($request->destinationId, $request->accountTypeDestination);

        return [
            'originAccount' => $originAccount,
            'destinationAccount' => $destinationAccount
        ];
    }

    /**
     * @param int $originId
     * @throws \DomainException
     * @return User
     */
    private function getOriginAccount(int $originId): User
    {
        $originAccount = $this->userRepository->getPayerUserById($originId);

        if (empty($originAccount)) {
            throw new \DomainException('Contas de origem não encontrada!');
        }
        return $originAccount;
    }

    /**
     * @param int $destinationAccountId
     * @param int $accountType
     * @throws \DomainException
     * @return User
     */
    private function getDestinationAccount(int $destinationAccountId, int $accountType): User
    {
        $destinationAccountAccount = $this
            ->userRepository
            ->getUserByAccountAndTypeId($destinationAccountId, $accountType);

        if (empty($destinationAccountAccount)) {
            throw new \DomainException('Contas de destino não encontrada!');
        }
        return $destinationAccountAccount;
    }
}
