<?php

namespace Tests\Unit\Services;

use App\Contracts\UserRepository;
use App\Http\Requests\TransferRequest;
use App\Models\User;
use App\Services\GetTransferAccountsByIdService;
use Tests\TestCase;

class GetTransferAccountsByIdServiceTest extends TestCase
{
    public function testGetTransferAccountsByIdSuccess(): void
    {
        /**
         * Given - Arrange
         */
        $payer = $this->makeUser(10);
        $payee = $this->makeUser(20);
        $request = $this->makeRequest(10, 20);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('getPayerUserById')->with(10)->willReturn($payer);
        $userRepositoryMock->method('getPayeeUserById')->with(20)->willReturn($payee);

        $service = new GetTransferAccountsByIdService($userRepositoryMock);

        /**
         * When - Act
         */
        $response = $service->getTransferAccountsById($request);

        /**
         * Then - Assert
         */
        $this->assertArrayHasKey('payer', $response, 'Chave payer ausente no retorno');
        $this->assertArrayHasKey('payee', $response, 'Chave payee ausente no retorno');
        $this->assertSame($payer, $response['payer'], 'Payer retornado incorretamente');
        $this->assertSame($payee, $response['payee'], 'Payee retornado incorretamente');
    }

    public function testGetTransferAccountsByIdFailsWhenPayerNotFound(): void
    {
        /**
         * Given - Arrange
         */
        $request = $this->makeRequest(10, 20);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('getPayerUserById')->willReturn(null);
        $userRepositoryMock->method('getPayeeUserById');

        $service = new GetTransferAccountsByIdService($userRepositoryMock);

        /**
         * Then - Assert
         */
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Conta de origem não encontrada!');

        /**
         * When - Act
         */
        $service->getTransferAccountsById($request);
    }

    public function testGetTransferAccountsByIdFailsWhenPayeeNotFound(): void
    {
        /**
         * Given - Arrange
         */
        $payer = $this->makeUser(10);
        $request = $this->makeRequest(10, 20);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('getPayerUserById')->willReturn($payer);
        $userRepositoryMock->method('getPayeeUserById')->willReturn(null);

        $service = new GetTransferAccountsByIdService($userRepositoryMock);

        /**
         * Then - Assert
         */
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Conta de destino não encontrada!');

        /**
         * When - Act
         */
        $service->getTransferAccountsById($request);
    }

    private function makeRequest(int $payerId, int $payeeId): TransferRequest
    {
        $request = new TransferRequest();
        $request->merge([
            'payer' => $payerId,
            'payee' => $payeeId,
        ]);

        return $request;
    }

    private function makeUser(int $id): User
    {
        $user = new User();
        $user->id = $id;

        return $user;
    }
}

