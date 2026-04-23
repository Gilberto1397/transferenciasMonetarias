<?php

namespace Tests\Feature\Repositories;

use App\Http\Requests\CreateAccountRequest;
use App\Models\FisicAccount;
use App\Models\JuristicAccount;
use App\Models\User;
use App\Repositories\UserRepositoryEloquent;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRepositoryEloquentTest extends TestCase
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

    public function testCreateUserSuccess(): void
    {
        /**
         * Given - Arrange
         */
        $request = $this->makeCreateUserRequest('User Success', 'user-success@mail.com', '123456789');
        $repository = new UserRepositoryEloquent();

        /**
         * When - Act
         */
        $createdUser = $repository->createUser($request);

        /**
         * Then - Assert
         */
        $this->assertInstanceOf(User::class, $createdUser);
        $this->assertNotEmpty($createdUser->id);
        $this->assertTrue(Hash::check('123456789', $createdUser->password));

        $this->assertDatabaseHas('users', [
            'id' => $createdUser->id,
            'name' => 'User Success',
            'email' => 'user-success@mail.com',
            'balance' => 0,
        ]);
    }

    public function testCreateUserThrowsExceptionWhenEmailIsDuplicated(): void
    {
        /**
         * Given - Arrange
         */
        $repository = new UserRepositoryEloquent();
        $firstRequest = $this->makeCreateUserRequest('First User', 'duplicate-user@mail.com', '123456789');
        $secondRequest = $this->makeCreateUserRequest('Second User', 'duplicate-user@mail.com', '987654321');

        $repository->createUser($firstRequest);

        /**
         * Then - Assert
         */
        $this->expectException(QueryException::class);

        /**
         * When - Act
         */
        $repository->createUser($secondRequest);
    }

    public function testGetPayerUserByIdReturnsFisicUser(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('payer-fisic@mail.com', 100.00);
        $this->createFisicAccount($user, '12312312312');

        $repository = new UserRepositoryEloquent();

        /**
         * When - Act
         */
        $result = $repository->getPayerUserById($user->id);

        /**
         * Then - Assert
         */
        $this->assertNotNull($result);
        $this->assertSame($user->id, $result->id);
    }

    public function testGetPayerUserByIdReturnsNullWhenUserIsNotFisic(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('payer-juristic@mail.com', 100.00);
        $this->createJuristicAccount($user, '12345678901234');

        $repository = new UserRepositoryEloquent();

        /**
         * When - Act
         */
        $result = $repository->getPayerUserById($user->id);

        /**
         * Then - Assert
         */
        $this->assertNull($result);
    }

    public function testGetPayeeUserByIdReturnsFisicUser(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('payee-fisic@mail.com');
        $this->createFisicAccount($user, '11111111111');

        $repository = new UserRepositoryEloquent();

        /**
         * When - Act
         */
        $result = $repository->getPayeeUserById($user->id);

        /**
         * Then - Assert
         */
        $this->assertNotNull($result);
        $this->assertSame($user->id, $result->id);
    }

    public function testGetPayeeUserByIdReturnsJuristicUser(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('payee-juristic@mail.com');
        $this->createJuristicAccount($user, '22222222222222');

        $repository = new UserRepositoryEloquent();

        /**
         * When - Act
         */
        $result = $repository->getPayeeUserById($user->id);

        /**
         * Then - Assert
         */
        $this->assertNotNull($result);
        $this->assertSame($user->id, $result->id);
    }

    public function testGetPayeeUserByIdReturnsNullWhenUserHasNoAccount(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('payee-no-account@mail.com');
        $repository = new UserRepositoryEloquent();

        /**
         * When - Act
         */
        $result = $repository->getPayeeUserById($user->id);

        /**
         * Then - Assert
         */
        $this->assertNull($result);
    }

    public function testGetPayeeUserByIdThrowsExceptionWhenUserHasFisicAndJuristicAccounts(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('payee-double-account@mail.com');
        $this->createFisicAccount($user, '33333333333');
        $this->createJuristicAccount($user, '33333333333333');

        $repository = new UserRepositoryEloquent();

        /**
         * Then - Assert
         */
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Mais de uma conta encontrada para o destinatário!');

        /**
         * When - Act
         */
        $repository->getPayeeUserById($user->id);
    }

    public function testGetPayeeUserByIdThrowsExceptionWhenFisicQueryReturnsMoreThanOneResult(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('payee-multi-fisic@mail.com');
        $this->createFisicAccount($user, '44444444444');
        $this->createFisicAccount($user, '55555555555');

        $repository = new UserRepositoryEloquent();

        /**
         * Then - Assert
         */
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Mais de uma conta encontrada para o destinatário!');

        /**
         * When - Act
         */
        $repository->getPayeeUserById($user->id);
    }

    public function testGetPayeeUserByIdThrowsExceptionWhenJuristicQueryReturnsMoreThanOneResult(): void
    {
        /**
         * Given - Arrange
         */
        $user = $this->createPersistedUser('payee-multi-juristic@mail.com');
        $this->createJuristicAccount($user, '44444444444');
        $this->createJuristicAccount($user, '55555555555');

        $repository = new UserRepositoryEloquent();

        /**
         * Then - Assert
         */
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Mais de uma conta encontrada para o destinatário!');

        /**
         * When - Act
         */
        $repository->getPayeeUserById($user->id);
    }

    public function testTransferValueSuccess(): void
    {
        /**
         * Given - Arrange
         */
        $payer = $this->createPersistedUser('payer-transfer@mail.com', 100.00);
        $payee = $this->createPersistedUser('payee-transfer@mail.com', 50.00);
        $repository = new UserRepositoryEloquent();

        /**
         * When - Act
         */
        $result = $repository->transferValue($payer, $payee, 25.00);

        /**
         * Then - Assert
         */
        $this->assertTrue($result);
        $this->assertSame(75.0, (float) $payer->fresh()->balance);
        $this->assertSame(75.0, (float) $payee->fresh()->balance);
    }

    private function makeCreateUserRequest(string $name, string $email, string $password): CreateAccountRequest
    {
        $request = new CreateAccountRequest();
        $request->merge([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        return $request;
    }

    private function createPersistedUser(string $email, float $balance = 0): User
    {
        $user = new User();
        $user->name = 'User Test';
        $user->email = $email;
        $user->password = '123456789';
        $user->balance = $balance;
        $user->save();

        return $user;
    }

    private function createFisicAccount(User $user, string $cpf): void
    {
        $fisicAccount = new FisicAccount();
        $fisicAccount->fisicaccount_accounttype = 2;
        $fisicAccount->fisicaccount_user = $user->id;
        $fisicAccount->fisicaccount_cpf = $cpf;
        $fisicAccount->save();
    }

    private function createJuristicAccount(User $user, string $cnpj): void
    {
        $juristicAccount = new JuristicAccount();
        $juristicAccount->juristicaccount_accounttype = 1;
        $juristicAccount->juristicaccount_user = $user->id;
        $juristicAccount->juristicaccount_cnpj = $cnpj;
        $juristicAccount->save();
    }
}

