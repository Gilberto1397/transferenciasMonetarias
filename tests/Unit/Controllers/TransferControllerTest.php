<?php

namespace Tests\Unit\Controllers;

use App\Helpers\OrganizeResponse;
use App\Http\Controllers\TransferController;
use App\Http\Requests\TransferRequest;
use App\Services\TransferValueService;
use Tests\TestCase;

class TransferControllerTest extends TestCase
{
    public function testTransferValueSuccess(): void
    {
        /**
         * Given - Arrange
         */
        $request = $this->createMock(TransferRequest::class);

        $response = new OrganizeResponse(200, 'Transferência realizada com sucesso!');

        $serviceMock = $this->createMock(TransferValueService::class);
        $serviceMock->expects($this->once())
            ->method('transferValue')
            ->willReturn($response);

        $controller = new TransferController();

        /**
         * When - Act
         */
        $jsonResponse = $controller->transferValue($request, $serviceMock);

        /**
         * Then - Assert
         */
        $this->assertSame(200, $jsonResponse->getStatusCode(), 'Código de status incorreto.');
        $this->assertSame(
            '{"message":"Transfer\u00eancia realizada com sucesso!","error":false}',
            $jsonResponse->getContent(),
            'Conteúdo JSON incorreto.'
        );
    }

    public function testTransferValueFailsWhenNotAuthorized(): void
    {
        /**
         * Given - Arrange
         */
        $request = $this->createMock(TransferRequest::class);

        $response = new OrganizeResponse(403, 'Transferência não autorizada!');

        $serviceMock = $this->createMock(TransferValueService::class);
        $serviceMock->expects($this->once())
            ->method('transferValue')
            ->willReturn($response);

        $controller = new TransferController();

        /**
         * When - Act
         */
        $jsonResponse = $controller->transferValue($request, $serviceMock);

        /**
         * Then - Assert
         */
        $this->assertSame(403, $jsonResponse->getStatusCode(), 'Código de status incorreto.');
        $this->assertSame(
            '{"message":"Transfer\u00eancia n\u00e3o autorizada!","error":true}',
            $jsonResponse->getContent(),
            'Conteúdo JSON incorreto.'
        );
    }

    public function testTransferValueFailsWhenBalanceIsInsufficient(): void
    {
        /**
         * Given - Arrange
         */
        $request = $this->createMock(TransferRequest::class);

        $response = new OrganizeResponse(500, 'Saldo insuficiente para realizar a transferência!');

        $serviceMock = $this->createMock(TransferValueService::class);
        $serviceMock->expects($this->once())
            ->method('transferValue')
            ->willReturn($response);

        $controller = new TransferController();

        /**
         * When - Act
         */
        $jsonResponse = $controller->transferValue($request, $serviceMock);

        /**
         * Then - Assert
         */
        $this->assertSame(500, $jsonResponse->getStatusCode(), 'Código de status incorreto.');

        if (!empty($jsonResponse->getContent())) {
            $this->assertStringContainsString(
                'Saldo insuficiente',
                $jsonResponse->getContent(),
                'Mensagem de erro faltando no JSON.'
            );
        }
    }

    public function testTransferValueFailsWhenAccountNotFound(): void
    {
        /**
         * Given - Arrange
         */
        $request = $this->createMock(TransferRequest::class);

        $response = new OrganizeResponse(500, 'Conta de origem não encontrada!');

        $serviceMock = $this->createMock(TransferValueService::class);
        $serviceMock->expects($this->once())
            ->method('transferValue')
            ->willReturn($response);

        $controller = new TransferController();

        /**
         * When - Act
         */
        $jsonResponse = $controller->transferValue($request, $serviceMock);

        /**
         * Then - Assert
         */
        $this->assertSame(500, $jsonResponse->getStatusCode(), 'Código de status incorreto.');

        if (!empty($jsonResponse->getContent())) {
            $this->assertStringContainsString(
                'Conta de origem',
                $jsonResponse->getContent(),
                'Mensagem de erro faltando no JSON.'
            );
        }

    }

    public function testTransferValueFailsWhenUnexpectedErrorOccurs(): void
    {
        /**
         * Given - Arrange
         */
        $request = $this->createMock(TransferRequest::class);

        $response = new OrganizeResponse(500, 'Ocorreu um erro ao processar a transferência!');

        $serviceMock = $this->createMock(TransferValueService::class);
        $serviceMock->expects($this->once())
            ->method('transferValue')
            ->willReturn($response);

        $controller = new TransferController();

        /**
         * When - Act
         */
        $jsonResponse = $controller->transferValue($request, $serviceMock);

        /**
         * Then - Assert
         */
        $this->assertSame(500, $jsonResponse->getStatusCode(), 'Código de status incorreto.');

        if (!empty($jsonResponse->getContent())) {
            $this->assertStringContainsString(
                'erro ao processar',
                $jsonResponse->getContent(),
                'Mensagem de erro faltando no JSON.'
            );
        }

    }
}

