<?php

namespace Tests\Feature\Controllers;

use App\Jobs\NotifyUserJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TransferControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        Queue::fake();
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
        $payload = [
            'value' => 10,
            'payer' => 11,
            'payee' => 5
        ];

        /**
         * When - Act
         */
        $response = $this->json('PUT', '/api/v1/transfer', $payload);
        $objetoResposta = json_decode($response->getContent());

        /**
         * Then - Assert
         */
        $this->assertObjectHasProperty('message', $objetoResposta, 'A resposta deve conter o atributo "message".');
        $this->assertObjectHasProperty('error', $objetoResposta, 'A resposta deve conter o atributo "error".');

        if ($response->getStatusCode() === 200) {
            $this->assertSame(200, $response->getStatusCode(), 'A resposta deve ter o status 200 OK.');
            $this->assertFalse($objetoResposta->error, "A propriedade 'error' deve ser falsa.");
            $this->assertSame(
                $objetoResposta->message,
                'Transferência realizada com sucesso!',
                "A mensagem de sucesso deve ser 'Transferência realizada com sucesso!'"
            );
            $this->assertTrue(Queue::hasPushed(NotifyUserJob::class), 'O job NotifyUserJob não foi despachado.');
            $this->assertCount(1, Queue::pushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado mais de uma vez.');
        } else {
            $this->assertSame(403, $response->getStatusCode(), 'A resposta deve ter o status 403.');
            $this->assertTrue($objetoResposta->error, "A propriedade 'error' deve ser falsa.");
            $this->assertSame(
                $objetoResposta->message,
                'Transferência não autorizada!',
                "A mensagem de sucesso deve ser 'Transferência não autorizada!'"
            );
        }
    }

    /**
     * @dataProvider invalidPayloadProvider
     */
    public function testCreateAccountValidationErrorWhenPayloadIsInvalid(array $payload): void
    {
        /**
         * Given - Arrange
         */
        $payload = [];

        /**
         * When - Act
         */
        $response = $this->json('PUT', '/api/v1/transfer', $payload);
        $objetoResposta = json_decode($response->getContent());

        /**
         * Then - Assert
         */
        $this->assertSame(406, $response->getStatusCode(), 'A resposta deve ter o status 406.');
        $this->assertObjectHasProperty('messages', $objetoResposta, 'A resposta deve conter o atributo "messages".');
        $this->assertObjectHasProperty('error', $objetoResposta, 'A resposta deve conter o atributo "error".');

        $this->assertTrue($objetoResposta->error, "A propriedade 'error' deve ser verdadeira.");
    }

    /**
     * @return array<string, array{0: array<string, int|string>, 1: string, 2: string, 3: string}>
     */
    private function invalidPayloadProvider(): array
    {

        return [
            'payer-invalid' => [
                [
                    'value' => 10,
                    'payer' => 5,
                    'payee' => 10
                ]
            ],
            'data-empty' => [
                []
            ],
        ];
    }
}

