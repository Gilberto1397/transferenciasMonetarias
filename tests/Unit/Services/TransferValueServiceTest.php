<?php

namespace Tests\Unit\Services;

use App\Clients\AuthorizationClient;
use App\Clients\NotificationClient;
use App\Contracts\UserRepository;
use App\Helpers\OrganizeResponse;
use App\Http\Requests\TransferRequest;
use App\Jobs\NotifyUserJob;
use App\Models\User;
use App\Services\GetTransferAccountsByIdService;
use App\Services\TransferValueService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Testing\Fakes\QueueFake;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class TransferValueServiceTest extends TestCase
{
    private NotificationClient $notificationClientMock;

    private QueueFake $queueFake;

    protected function setUp(): void
    {
        parent::setUp();

        /**
         * @returns QueueFake
         */
        $this->queueFake = Queue::fake();
        $this->notificationClientMock = $this->createMock(NotificationClient::class);
        $this->notificationClientMock
            ->expects($this->never())
            ->method('throwNotification')
            ->willReturn(true);
    }

    public function testTransferValueSuccess(): void
    {
        /**
         * Given - Arrange
         */

        $request = $this->makeRequest(10, 20, 50);
        $payer = $this->makeUser(10, 100);
        $payee = $this->makeUser(20, 10);

        $authorizationClientMock = $this->createMock(AuthorizationClient::class);
        $authorizationClientMock
            ->expects($this->once())
            ->method('checkAuthorization')
            ->willReturn(true);

        $accountsServiceMock = $this->createMock(GetTransferAccountsByIdService::class);
        $accountsServiceMock
            ->expects($this->once())
            ->method('getTransferAccountsById')
            ->willReturn([
                'payer' => $payer,
                'payee' => $payee,
            ]);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('transferValue');

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();

        $service = new TransferValueService(
            $userRepositoryMock,
            $accountsServiceMock,
            $authorizationClientMock,
            $this->notificationClientMock
        );

        /**
         * When - Act
         */
        $response = $service->transferValue($request);

        /**
         * Then - Assert
         */
        $this->assertInstanceOf(OrganizeResponse::class, $response, 'Resposta não é uma instância de OrganizeResponse');
        $this->assertSame(200, $response->getStatusCode(), 'Código de status incorreto');
        $this->assertSame('Transferência realizada com sucesso!', $response->getMessage(), 'Mensagem de sucesso incorreta');
        $this->assertTrue($this->queueFake->hasPushed(NotifyUserJob::class), 'O job NotifyUserJob não foi despachado.');
        $this->assertCount(1, $this->queueFake->pushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado mais de uma vez.');
    }

    public function testTransferValueFailsWhenNotAuthorized(): void
    {
        /**
         * Given - Arrange
         */

        $request = $this->makeRequest(10, 20, 50);

        $authorizationClientMock = $this->createMock(AuthorizationClient::class);
        $authorizationClientMock
            ->expects($this->once())
            ->method('checkAuthorization')
            ->willReturn(false);

        $accountsServiceMock = $this->createMock(GetTransferAccountsByIdService::class);
        $accountsServiceMock->expects($this->never())
            ->method('getTransferAccountsById');

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock
            ->expects($this->never())
            ->method('transferValue');

        DB::shouldReceive('beginTransaction')->never();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollBack')->never();

        $service = new TransferValueService(
            $userRepositoryMock,
            $accountsServiceMock,
            $authorizationClientMock,
            $this->notificationClientMock
        );

        /**
         * When - Act
         */
        $response = $service->transferValue($request);

        /**
         * Then - Assert
         */
        $this->assertInstanceOf(OrganizeResponse::class, $response, 'Resposta não é uma instância de OrganizeResponse');
        $this->assertSame(403, $response->getStatusCode(), 'Código de status incorreto');
        $this->assertSame('Transferência não autorizada!', $response->getMessage(), 'Mensagem de bloqueio incorreta');
        $this->assertFalse($this->queueFake->hasPushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado.');
        $this->assertCount(0, $this->queueFake->pushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado alguma vez.');
    }

    public function testTransferValueFailsWhenBalanceIsInsufficient(): void
    {
        /**
         * Given - Arrange
         */

        $request = $this->makeRequest(10, 20, 50);
        $payer = $this->makeUser(10, 40);
        $payee = $this->makeUser(20, 10);

        $authorizationClientMock = $this->createMock(AuthorizationClient::class);
        $authorizationClientMock
            ->expects($this->once())
            ->method('checkAuthorization')
            ->willReturn(true);

        $accountsServiceMock = $this->createMock(GetTransferAccountsByIdService::class);
        $accountsServiceMock
            ->expects($this->once())
            ->method('getTransferAccountsById')
            ->willReturn([
                'payer' => $payer,
                'payee' => $payee,
            ]);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock
            ->expects($this->never())
            ->method('transferValue');

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();
        DB::shouldReceive('commit')->never();

        Log::shouldReceive('error')
            ->once()
            ->with(
                'Saldo insuficiente para realizar a transferência!',
                \Mockery::on(function (array $context): bool {
                    return isset($context['file'], $context['line']);
                })
            );

        $service = new TransferValueService(
            $userRepositoryMock,
            $accountsServiceMock,
            $authorizationClientMock,
            $this->notificationClientMock
        );

        /**
         * When - Act
         */
        $response = $service->transferValue($request);

        /**
         * Then - Assert
         */
        $this->assertInstanceOf(OrganizeResponse::class, $response, 'Resposta não é uma instância de OrganizeResponse');
        $this->assertSame(500, $response->getStatusCode(), 'Código de status incorreto');
        $this->assertSame('Saldo insuficiente para realizar a transferência!', $response->getMessage(), 'Mensagem de erro incorreta');
        $this->assertFalse($this->queueFake->hasPushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado.');
        $this->assertCount(0, $this->queueFake->pushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado alguma vez.');
    }

    public function testTransferValueFailsWhenAccountLookupThrowsDomainException(): void
    {
        /**
         * Given - Arrange
         */

        $request = $this->makeRequest(10, 20, 50);

        $authorizationClientMock = $this->createMock(AuthorizationClient::class);
        $authorizationClientMock
            ->expects($this->once())
            ->method('checkAuthorization')
            ->willReturn(true);

        $accountsServiceMock = $this->createMock(GetTransferAccountsByIdService::class);
        $accountsServiceMock
            ->expects($this->once())
            ->method('getTransferAccountsById')
            ->willThrowException(new \DomainException('Conta de origem não encontrada!'));

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->expects($this->never())
            ->method('transferValue');

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();
        DB::shouldReceive('commit')->never();

        Log::shouldReceive('error')
            ->once()
            ->with(
                'Conta de origem não encontrada!',
                \Mockery::on(function (array $context): bool {
                    return isset($context['file'], $context['line']);
                })
            );

        $service = new TransferValueService(
            $userRepositoryMock,
            $accountsServiceMock,
            $authorizationClientMock,
            $this->notificationClientMock
        );

        /**
         * When - Act
         */
        $response = $service->transferValue($request);

        /**
         * Then - Assert
         */
        $this->assertInstanceOf(OrganizeResponse::class, $response, 'Resposta não é uma instância de OrganizeResponse');
        $this->assertSame(500, $response->getStatusCode(), 'Código de status incorreto');
        $this->assertSame('Conta de origem não encontrada!', $response->getMessage(), 'Mensagem de erro incorreta');
        $this->assertFalse($this->queueFake->hasPushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado.');
        $this->assertCount(0, $this->queueFake->pushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado alguma vez.');
    }

    public function testTransferValueFailsWhenTransferRepositoryThrowsThrowable(): void
    {
        /**
         * Given - Arrange
         */

        $request = $this->makeRequest(10, 20, 50);
        $payer = $this->makeUser(10, 100);
        $payee = $this->makeUser(20, 10);

        $authorizationClientMock = $this->createMock(AuthorizationClient::class);
        $authorizationClientMock
            ->expects($this->once())
            ->method('checkAuthorization')
            ->willReturn(true);

        $accountsServiceMock = $this->createMock(GetTransferAccountsByIdService::class);
        $accountsServiceMock
            ->expects($this->once())
            ->method('getTransferAccountsById')
            ->willReturn([
                'payer' => $payer,
                'payee' => $payee,
            ]);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock
            ->expects($this->once())
            ->method('transferValue')
            ->willThrowException(new \Exception('Erro inesperado ao transferir valor!'));

        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollBack')->once();
        DB::shouldReceive('commit')->never();

        Log::shouldReceive('error')
            ->once()
            ->with(
                'Erro inesperado ao transferir valor!',
                \Mockery::on(function (array $context): bool {
                    return isset($context['file'], $context['line']);
                })
            );

        $service = new TransferValueService(
            $userRepositoryMock,
            $accountsServiceMock,
            $authorizationClientMock,
            $this->notificationClientMock
        );

        /**
         * When - Act
         */
        $response = $service->transferValue($request);

        /**
         * Then - Assert
         */
        $this->assertInstanceOf(OrganizeResponse::class, $response, 'Resposta não é uma instância de OrganizeResponse');
        $this->assertSame(500, $response->getStatusCode(), 'Código de status incorreto');
        $this->assertSame('Ocorreu um erro ao processar a transferência!', $response->getMessage(), 'Mensagem fallback incorreta');
        $this->assertFalse($this->queueFake->hasPushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado.');
        $this->assertCount(0, $this->queueFake->pushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado alguma vez.');
    }

    private function makeRequest(int $payerId, int $payeeId, float $value): TransferRequest
    {
        $request = new TransferRequest();
        $request->merge([
            'value' => $value,
            'payer' => $payerId,
            'payee' => $payeeId,
        ]);

        return $request;
    }

    private function makeUser(int $id, float $balance): User
    {
        $user = new User();
        $user->id = $id;
        $user->balance = $balance;

        return $user;
    }
}

