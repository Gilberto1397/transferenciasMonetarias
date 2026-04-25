<?php

namespace Tests\Feature\Repositories;

use App\Http\Requests\CreateAccountRequest;
use App\Models\User;
use App\Repositories\JuristicAccountRepositoryEloquent;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class JuristicAccountRepositoryEloquentTest extends TestCase
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
        $user = $this->createPersistedUser('juristic-success@mail.com');
        $request = $this->makeRequest('12345678901234', 1);

        $repository = new JuristicAccountRepositoryEloquent();

        /**
         * When - Act
         */
        $created = $repository->createAccount($request, $user);

        /**
         * Then - Assert
         */
        $this->assertTrue($created, 'A conta jurídica deveria ser criada com sucesso.');

        $this->assertDatabaseHas('juristicaccounts', [
            'juristicaccount_cnpj' => '12345678901234',
            'juristicaccount_user' => $user->id,
            'juristicaccount_accounttype' => 1,
        ]);
    }

    public function testCreateAccountThrowsExceptionWhenCnpjIsDuplicated(): void
    {
        /**
         * Given - Arrange
         */
        $firstUser = $this->createPersistedUser('juristic-duplicate-1@mail.com');
        $secondUser = $this->createPersistedUser('juristic-duplicate-2@mail.com');

        $repository = new JuristicAccountRepositoryEloquent();
        $firstRequest = $this->makeRequest('55566677788899', 1);
        $secondRequest = $this->makeRequest('55566677788899', 1);

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

        $request = $this->makeRequest('11223344556677', 1);
        $repository = new JuristicAccountRepositoryEloquent();

        /**
         * Then - Assert
         */
        $this->expectException(QueryException::class);

        /**
         * When - Act
         */
        $repository->createAccount($request, $user);
    }

    private function makeRequest(string $cnpj, int $tipoConta): CreateAccountRequest
    {
        $request = new CreateAccountRequest();
        $request->merge([
            'cnpj' => $cnpj,
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

