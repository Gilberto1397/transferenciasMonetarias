<?php

namespace Tests\Feature\Controllers;

use App\Jobs\NotifyUserJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Testing\Fakes\QueueFake;
use Tests\TestCase;

class TransferControllerTest extends TestCase
{
    private QueueFake $queueFake;
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
        $this->queueFake = Queue::fake();
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

        if (!empty($response->getContent())) {

            /**
             * @var \StdClass $objetoResposta
             */
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
                $this->assertTrue($this->queueFake->hasPushed(NotifyUserJob::class), 'O job NotifyUserJob não foi despachado.');
                $this->assertCount(1, $this->queueFake->pushed(NotifyUserJob::class), 'O job NotifyUserJob foi despachado mais de uma vez.');
            } else {
                dump($response);
                $this->assertSame(403, $response->getStatusCode(), 'A resposta deve ter o status 403.');
                $this->assertTrue($objetoResposta->error, "A propriedade 'error' deve ser falsa.");
                $this->assertSame(
                    $objetoResposta->message,
                    'Transferência não autorizada!',
                    "A mensagem de sucesso deve ser 'Transferência não autorizada!'"
                );
            }
        }
    }

    /**
     * @dataProvider invalidPayloadProvider
     * @param array<string, int|string> $payload
     */
    public function testCreateAccountValidationErrorWhenPayloadIsInvalid(array $payload): void
    {
        /**
         * When - Act
         */
        $response = $this->json('PUT', '/api/v1/transfer', $payload);

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
     * @return array{payer-invalid: array{array{value: 10, payer: 5, payee: 10}}, data-empty: array{array{}}}
     */
    public function invalidPayloadProvider(): array
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

