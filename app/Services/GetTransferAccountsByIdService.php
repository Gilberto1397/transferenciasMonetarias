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
        $payer = $this->getPayer($request->payer);
        $payee = $this->getPayee($request->payee);

        return [
            'payer' => $payer,
            'payee' => $payee
        ];
    }

    /**
     * @param int $payerId
     * @return User
     * @throws \DomainException
     */
    private function getPayer(int $payerId): User
    {
        $payer = $this->userRepository->getPayerUserById($payerId);

        if (empty($payer)) {
            throw new \DomainException('Conta de origem não encontrada!');
        }
        return $payer;
    }

    /**
     * @param int $payeeId
     * @return User
     * @throws \DomainException
     */
    private function getPayee(int $payeeId): User
    {
        $payee = $this
            ->userRepository
            ->getPayeeUserById($payeeId);

        if (empty($payee)) {
            throw new \DomainException('Conta de destino não encontrada!');
        }
        return $payee;
    }
}
