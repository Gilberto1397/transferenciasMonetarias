<?php

namespace Tests\Unit\Services;

use App\Contracts\FisicAccountRepository;
use App\Contracts\JuristicAccountRepository;
use App\Contracts\UserRepository;
use App\Helpers\OrganizeResponse;
use App\Http\Requests\CreateAccountRequest;
use App\Models\User;
use App\Services\CreateAccountService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CreateAccountServiceTest extends TestCase
{
    /**
     * @dataProvider dataRequest
     * @return void
     */
    public function testCreateAccountSuccess(CreateAccountRequest $request, User $user)
    {
        /**
         * Given - Arrange
         */
        $juristicAccountRepositoryMock = $this->createMock(JuristicAccountRepository::class);
        $juristicAccountRepositoryMock->method('createAccount')->willReturn(true);

        $fisicAccountRepositoryMock = $this->createMock(FisicAccountRepository::class);
        $fisicAccountRepositoryMock->method('createAccount')->willReturn(true);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('createUser')->willReturn($user);

        $createAccountService = new CreateAccountService(
            $juristicAccountRepositoryMock,
            $fisicAccountRepositoryMock,
            $userRepositoryMock
        );

        /**
         * When - Act
         */
        $response = $createAccountService->createAccount($request);

        /**
         * Then - Assert
         */
        $this->assertInstanceOf(
            OrganizeResponse::class,
            $response,
            'Resposta não é uma instância de OrganizeResponse'
        );

        $this->assertEquals(
            'Conta criada com sucesso!',
            $response->getMessage(),
            'Mensagem de sucesso incorreta'
        );

        $this->assertEquals(201, $response->getStatusCode(), 'Código de status incorreto');
    }

    /**
     * @dataProvider dataRequest
     * @return void
     */
    public function testCreateAccountUserFailed(CreateAccountRequest $request,)
    {
        /**
         * Given - Arrange
         */
        $juristicAccountRepositoryMock = $this->createMock(JuristicAccountRepository::class);
        $juristicAccountRepositoryMock->method('createAccount')->willReturn(true);

        $fisicAccountRepositoryMock = $this->createMock(FisicAccountRepository::class);
        $fisicAccountRepositoryMock->method('createAccount')->willReturn(true);

        $userException = new \DomainException('Não foi possível criar o titular da conta!');
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('createUser')->willThrowException($userException);

        $createAccountService = new CreateAccountService(
            $juristicAccountRepositoryMock,
            $fisicAccountRepositoryMock,
            $userRepositoryMock
        );
        Log::shouldReceive('error')
            ->once()
            ->with(
                'Não foi possível criar o titular da conta!',
                \Mockery::on(function (array $context) {
                    return isset($context['file'], $context['line']);
                }));

        /**
         * When - Act
         */
        $response = $createAccountService->createAccount($request);

        /**
         * Then - Assert
         */
        /**
         * Then - Assert
         */
        $this->assertInstanceOf(
            OrganizeResponse::class,
            $response,
            'Resposta não é uma instância de OrganizeResponse'
        );

        $this->assertEquals(
            'Não foi possível criar o titular da conta!',
            $response->getMessage(),
            'Mensagem de erro incorreta'
        );

        $this->assertEquals(500, $response->getStatusCode(), 'Código de status incorreto');

    }

    /**
     * @dataProvider dataRequest
     * @return void
     */
    public function testCreateAccountFailed(CreateAccountRequest $request, User $user)
    {
        /**
         * Given - Arrange
         */
        $juristicAccountRepositoryMock = $this->createMock(JuristicAccountRepository::class);
        $juristicAccountRepositoryMock->method('createAccount')->willReturn(false);

        $fisicAccountRepositoryMock = $this->createMock(FisicAccountRepository::class);
        $fisicAccountRepositoryMock->method('createAccount')->willReturn(false);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('createUser')->willReturn($user);

        $createAccountService = new CreateAccountService(
            $juristicAccountRepositoryMock,
            $fisicAccountRepositoryMock,
            $userRepositoryMock
        );
        Log::shouldReceive('error')
            ->once()
            ->with(
                'Erro ao criar conta bancária!',
                \Mockery::on(function (array $context) {
                    return isset($context['file'], $context['line']);
                }));

        /**
         * When - Act
         */
        $response = $createAccountService->createAccount($request);

        /**
         * Then - Assert
         */
        /**
         * Then - Assert
         */
        $this->assertInstanceOf(
            OrganizeResponse::class,
            $response,
            'Resposta não é uma instância de OrganizeResponse'
        );

        $this->assertEquals(
            'Erro ao criar conta bancária!',
            $response->getMessage(),
            'Mensagem de erro incorreta'
        );

        $this->assertEquals(500, $response->getStatusCode(), 'Código de status incorreto');

    }

    /**
     * @dataProvider dataRequest
     * @return void
     */
    public function testCreateAccountException(CreateAccountRequest $request, User $user)
    {
        /**
         * Given - Arrange
         */
        $juristicAccountRepositoryMock = $this->createMock(JuristicAccountRepository::class);
        $juristicAccountRepositoryMock->method('createAccount')->willReturn(true);

        $fisicAccountRepositoryMock = $this->createMock(FisicAccountRepository::class);
        $fisicAccountRepositoryMock->method('createAccount')->willReturn(true);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('createUser')->willThrowException(new \Exception('Erro inesperado ao criar usuário!'));

        $createAccountService = new CreateAccountService(
            $juristicAccountRepositoryMock,
            $fisicAccountRepositoryMock,
            $userRepositoryMock
        );
        Log::shouldReceive('error')
            ->once()
            ->with(
                'Erro inesperado ao criar usuário!',
                \Mockery::on(function (array $context) {
                    return isset($context['file'], $context['line']);
                }));

        /**
         * When - Act
         */
        $response = $createAccountService->createAccount($request);

        /**
         * Then - Assert
         */
        /**
         * Then - Assert
         */
        $this->assertInstanceOf(
            OrganizeResponse::class,
            $response,
            'Resposta não é uma instância de OrganizeResponse'
        );

        $this->assertEquals(
            'Houve um erro ao criar conta bancária!',
            $response->getMessage(),
            'Mensagem de erro incorreta'
        );

        $this->assertEquals(500, $response->getStatusCode(), 'Código de status incorreto');

    }

    public function dataRequest()
    {
        $user = new User();
        $user->id = 999;

        $juristicRequest = new CreateAccountRequest();
        $juristicRequest->tipoConta = 1;

        $fisicRequest = new CreateAccountRequest();
        $fisicRequest->tipoConta = 2;

        return [
            'fisicRequest' => [$fisicRequest, $user],
            'juristicRequest ' => [$juristicRequest, $user]
        ];
    }
}
