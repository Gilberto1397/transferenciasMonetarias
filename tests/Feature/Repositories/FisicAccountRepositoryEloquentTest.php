<?php

namespace Tests\Feature\Repositories;

use App\Http\Requests\CreateAccountRequest;
use App\Models\User;
use App\Repositories\FisicAccountRepositoryEloquent;
use Database\Seeders\AccountTypeSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FisicAccountRepositoryEloquentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function testCreateAccountSuccess(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('fisic-success@mail.com');
        $request = $this->makeRequest('12345678901', 2);

        $repository = new FisicAccountRepositoryEloquent();

        /**
         * When - Act
         */
        $created = $repository->createAccount($request, $user);

        /**
         * Then - Assert
         */
        $this->assertTrue($created, 'A conta física deveria ser criada com sucesso.');

        $this->assertDatabaseHas('fisicaccounts', [
            'fisicaccount_cpf' => '12345678901',
            'fisicaccount_user' => $user->id,
            'fisicaccount_accounttype' => 2,
        ]);
    }

    public function testCreateAccountThrowsExceptionWhenCpfIsDuplicated(): void
    {
        /**
         * Given - Arrange
         */
        $firstUser = $this->createPersistedUser('fisic-duplicate-1@mail.com');
        $secondUser = $this->createPersistedUser('fisic-duplicate-2@mail.com');

        $repository = new FisicAccountRepositoryEloquent();
        $firstRequest = $this->makeRequest('55566677788', 2);
        $secondRequest = $this->makeRequest('55566677788', 2);

        $repository->createAccount($firstRequest, $firstUser);

        /**
         * Then - Assert
         */
        $this->expectException(QueryException::class);

        /**
         * When - Act
         */
        $repository->createAccount($secondRequest, $secondUser);
    }

    public function testCreateAccountThrowsExceptionWhenUserDoesNotExist(): void
    {
        /**
         * Given - Arrange
         */
        $user = new User();
        $user->id = 999999;

        $request = $this->makeRequest('11223344556', 2);
        $repository = new FisicAccountRepositoryEloquent();

        /**
         * Then - Assert
         */
        $this->expectException(QueryException::class);

        /**
         * When - Act
         */
        $repository->createAccount($request, $user);
    }

    private function makeRequest(string $cpf, int $tipoConta): CreateAccountRequest
    {
        $request = new CreateAccountRequest();
        $request->merge([
            'cpf' => $cpf,
            'tipoConta' => $tipoConta,
        ]);

        return $request;
    }

    private function createPersistedUser(string $email): User
    {
        $user = new User();
        $user->name = 'User Test';
        $user->email = $email;
        $user->password = '123456789';
        $user->balance = 0;
        $user->save();

        return $user;
    }
}

