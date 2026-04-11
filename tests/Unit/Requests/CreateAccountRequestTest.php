<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\CreateAccountRequest;
use App\Models\FisicAccount;
use App\Models\JuristicAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CreateAccountRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();

        $fisicAccount = new FisicAccount();
        $fisicAccount->fisicaccount_accounttype = 2;
        $fisicAccount->fisicaccount_cpf = '12345678901';
        $fisicAccount->fisicaccount_user = 3;
        $fisicAccount->save();

        $juristicAccount = new JuristicAccount();
        $juristicAccount->juristicaccount_accounttype = 1;
        $juristicAccount->juristicaccount_cnpj = '12345678901234';
        $juristicAccount->juristicaccount_user = 3;
        $juristicAccount->save();

        $user = new User();
        $user->name = 'Existing User';
        $user->email = 'mail@mail.com';
        $user->password = '123456789';
        $user->balance = 0;
        $user->save();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /**
     * @return void
     * @test
     * The following checks are made:
     * - The data should be accepted by the validation rules.
     */
    public function testAcceptedData(): void
    {
        /**
         * Arrange - Given
         */
        $data1 = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'tipoConta' => 1,
            'cnpj' => '99988855632015',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $data2 = [
            'name' => 'Empresa LTDA',
            'email' => 'empresa@example.com',
            'tipoConta' => 2,
            'cpf' => '66644455598',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $dataSet = [$data1, $data2];

        $request = new CreateAccountRequest();

        foreach ($dataSet as $item) {
            /**
             * Act - When
             */
            $validator = Validator::make($item, $request->rules(), $request->messages());

            /**
             * Assert - Then
             */
            $this->assertFalse($validator->fails(), "Data not accepted.");
        }
    }

    /**
     * @param string $fieldName
     * @param array<string, mixed> $dataRequest
     * @param string $expectedMessage
     * @return void
     * @test
     * @dataProvider nameNotAccepted
     * @dataProvider emailNotAccepted
     * @dataProvider tipoContaNotAccepted
     * @dataProvider cpfNotAccepted
     * @dataProvider cnpjNotAccepted
     * @dataProvider passwordNotAccepted
     * @dataProvider passwordConfirmationNotAccepted
     * The following checks are made:
     * - The data should not be accepted by the validation rules.
     * - The error message should match the expected message.
     */
    public function testDataNotAccepted(string $fieldName, array $dataRequest, string $expectedMessage): void
    {
        /**
         * Arrange - Given
         */
        $createAccountRequest = new CreateAccountRequest();
        $validator = Validator::make($dataRequest, $createAccountRequest->rules(), $createAccountRequest->messages());

        /**
         * Act - When
         */
        $validated = $validator->fails();
        $firstMessage = $validator->messages()->get($fieldName)[0];

        /**
         * Assert - Then
         */
        $this->assertTrue($validated, "The data should not be accepted - '{$fieldName}'");
        $this->assertSame($firstMessage, $expectedMessage, "Wrong error message for '{$fieldName}'");
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function nameNotAccepted(): array
    {
        $name1 = [
            'name' => '',
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $name2 = [
            'name' => 123,
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $name3 = [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        return [
            "CreateAccountRequest - name = ''" => ['name', $name1, 'É necessário informar o nome do titular da conta!'],
            'CreateAccountRequest - name = 123' => ['name', $name2, 'O campo nome não possui um formato válido!'],
            'CreateAccountRequest - name = 256 chars' => ['name', $name3, 'O campo nome deve conter no máximo 255 caracteres!'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function emailNotAccepted(): array
    {
        $email1 = [
            'name' => 'Test User',
            'email' => '',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $email2 = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $email3 = [
            'name' => 'Test User',
            'email' => str_repeat('a', 250) . '@example.com',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $email4 = [
            'name' => 'Test User',
            'email' => 'mail@mail.com',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        return [
            "CreateAccountRequest - email = ''" => ['email', $email1, 'É necessário informar um email para a conta!'],
            'CreateAccountRequest - email = invalid format' => ['email', $email2, 'O campo email deve ser um email válido!'],
            'CreateAccountRequest - email = 256 chars' => ['email', $email3, 'O campo email deve conter no máximo 255 caracteres!'],
            'CreateAccountRequest - email already exists' => ['email', $email4, 'O email já está em uso!']
        ];
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function tipoContaNotAccepted(): array
    {
        $tipoConta1 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $tipoConta2 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 'invalid',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        return [
            "CreateAccountRequest - tipoConta = ''" => ['tipoConta', $tipoConta1, 'É necessário informar o tipo da conta!'],
            'CreateAccountRequest - tipoConta = invalid' => ['tipoConta', $tipoConta2, 'É necessário informar o tipo da conta!'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function cpfNotAccepted(): array
    {
        $cpf1 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'cpf' => '123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $cpf2 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'cpf' => 'abcdefghijk',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $cpf3 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'cpf' => '12345678901',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        return [
            "CreateAccountRequest - cpf = 9 digits" => ['cpf', $cpf1, 'O campo cpf deve conter 11 caracteres!'],
            'CreateAccountRequest - cpf = letters' => ['cpf', $cpf2, 'O campo cpf deve conter apenas números!'],
            'CreateAccountRequest - cpf already exists' => ['cpf', $cpf3, 'Já existe uma conta para esse cpf!']
        ];
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function cnpjNotAccepted(): array
    {
        $cnpj1 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 2,
            'cnpj' => '123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $cnpj2 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 2,
            'cnpj' => 'invalid-cnpj',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $cnpj3 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 2,
            'cnpj' => '12345678901234',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        return [
            'CreateAccountRequest - cnpj = 9 digits' => ['cnpj', $cnpj1, 'O campo cnpj deve conter 14 caracteres!'],
            'CreateAccountRequest - cnpj = invalid format' => ['cnpj', $cnpj2, 'O campo cnpj está inválido!'],
            'CreateAccountRequest - cnpj already exists' => ['cnpj', $cnpj3, 'Já existe uma conta para esse cnpj!']
        ];
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function passwordNotAccepted(): array
    {
        $password1 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'password' => '',
            'password_confirmation' => '',
        ];

        $password2 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ];

        $password3 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ];

        return [
            "CreateAccountRequest - password = ''" => ['password', $password1, 'É necessário informar uma senha para a conta!'],
            'CreateAccountRequest - password = 7 chars' => ['password', $password2, 'O campo senha deve conter no mínimo 8 caracteres!'],
            'CreateAccountRequest - password != confirmation' => ['password', $password3, 'A confirmação de senha não corresponde à senha informada!'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function passwordConfirmationNotAccepted(): array
    {
        $passwordConfirmation1 = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tipoConta' => 1,
            'password' => 'password123',
            'password_confirmation' => '',
        ];

        return [
            "CreateAccountRequest - password_confirmation = ''" => ['password_confirmation', $passwordConfirmation1, 'É necessário confirmar a senha!'],
        ];
    }
}

