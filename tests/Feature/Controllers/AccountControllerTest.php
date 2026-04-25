<?php

namespace Tests\Feature\Controllers;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountControllerTest extends TestCase
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

    /**
     * @dataProvider validPayloadProvider
     * @param array<string, int|string> $payload
     * @param string $accountTable
     * @param string $documentColumn
     * @param string $documentValue
     */
    public function testCreateAccountSuccess(array $payload, string $accountTable, string $documentColumn, string $documentValue): void
    {
        /**
         * Given - Arrange
         */

        /**
         * When - Act
         */
        $response = $this->json('POST', '/api/v1/contas', $payload);

        if (!empty($response->getContent())) {
            /**
             * @var \StdClass $objetoResposta
             */
            $objetoResposta = json_decode($response->getContent());

            /**
             * Then - Assert
             */
            $this->assertSame(201, $response->getStatusCode(), 'A resposta deve ter o status 201 Created.');
            $this->assertObjectHasProperty('message', $objetoResposta, 'A resposta deve conter o atributo "message".');
            $this->assertObjectHasProperty('error', $objetoResposta, 'A resposta deve conter o atributo "error".');

            $this->assertFalse($objetoResposta->error, "A propriedade 'error' deve ser falsa.");

            $this->assertSame(
                $objetoResposta->message,
                'Conta criada com sucesso!',
                "A mensagem de sucesso deve ser 'Conta criada com sucesso!'"
            );

            $this->assertDatabaseHas('users', [
                'email' => $payload['email'],
                'name' => $payload['name'],
            ]);

            $userId = DB::table('users')->where('email', $payload['email'])->value('id');

            $this->assertDatabaseHas($accountTable, [
                $documentColumn => $documentValue,
                $accountTable === 'fisicaccounts' ? 'fisicaccount_user' : 'juristicaccount_user' => $userId,
                $accountTable === 'fisicaccounts' ? 'fisicaccount_accounttype' : 'juristicaccount_accounttype' => $payload['tipoConta'],
            ]);
        }
    }

    public function testCreateAccountValidationErrorWhenPayloadIsInvalid(): void
    {
        /**
         * Given - Arrange
         */
        $payload = [];

        /**
         * When - Act
         */
        $response = $this->json('POST', '/api/v1/contas', $payload);

        if (!empty($response->getContent())) {
            /**
             * @var \StdClass $objetoResposta
             */

            $objetoResposta = json_decode($response->getContent());

            /**
             * Then - Assert
             */
            $this->assertSame(406, $response->getStatusCode(), 'A resposta deve ter o status 406.');
            $this->assertObjectHasProperty('messages', $objetoResposta, 'A resposta deve conter o atributo "messages".');
            $this->assertObjectHasProperty('error', $objetoResposta, 'A resposta deve conter o atributo "error".');

            $this->assertTrue($objetoResposta->error, "A propriedade 'error' deve ser verdadeira.");
        }
    }

    /**
     * @return array{fisicAccount: array{array<string, int|string>, 'fisicaccounts', 'fisicaccount_cpf', int|string},
     * juristicAccount: array{array<string, int|string>, 'juristicaccounts', 'juristicaccount_cnpj', int|string}}
     */
    public function validPayloadProvider(): array
    {
        $fisicPayload = $this->validFisicPayload();
        $juristicPayload = $this->validJuristicPayload();

        return [
            'fisicAccount' => [
                $fisicPayload,
                'fisicaccounts',
                'fisicaccount_cpf',
                $fisicPayload['cpf'],
            ],
            'juristicAccount' => [
                $juristicPayload,
                'juristicaccounts',
                'juristicaccount_cnpj',
                $juristicPayload['cnpj'],
            ],
        ];
    }

    /**
     * @return array<string, int|string>
     */
    private function validFisicPayload(): array
    {
        return [
            'name' => 'Fulano de Tal',
            'email' => 'fisic' . uniqid('', true) . '@mail.com',
            'tipoConta' => 2,
            'cpf' => '12345678901',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];
    }

    /**
     * @return array<string, int|string>
     */
    private function validJuristicPayload(): array
    {
        return [
            'name' => 'Empresa X',
            'email' => 'juristic' . uniqid('', true) . '@mail.com',
            'tipoConta' => 1,
            'cnpj' => '12345678901234',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ];
    }
}

