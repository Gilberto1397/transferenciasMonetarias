<?php

namespace Tests\Unit\Helpers;

use App\Helpers\OrganizeResponse;
use Tests\TestCase;

class OrganizeResponseTest extends TestCase
{
    public function testConstructorStoresProvidedValues(): void
    {
        /**
         * Given - Arrange
         */
        $data = ['transferId' => 10, 'status' => 'ok'];

        /**
         * When - Act
         */
        $response = new OrganizeResponse(201, 'Conta criada com sucesso!', $data);

        /**
         * Then - Assert
         */
        $this->assertSame(201, $response->getStatusCode(), 'Código de status incorreto.');
        $this->assertSame('Conta criada com sucesso!', $response->getMessage(), 'Mensagem incorreta.');
        $this->assertSame($data, $response->getData(), 'Dados incorretos.');
        $this->assertFalse($response->getError(), 'Flag de erro incorreta para status de sucesso.');
    }

    public function testConstructorUsesDefaultMessageAndData(): void
    {
        /**
         * When - Act
         */
        $response = new OrganizeResponse(204);

        /**
         * Then - Assert
         */
        $this->assertSame(204, $response->getStatusCode(), 'Código de status incorreto.');
        $this->assertSame('', $response->getMessage(), 'Mensagem padrão incorreta.');
        $this->assertNull($response->getData(), 'Dados padrão deveriam ser nulos.');
        $this->assertFalse($response->getError(), 'Flag de erro incorreta para status menor que 400.');
    }

    public function testGetErrorReturnsFalseForStatusBelow400(): void
    {
        $response = new OrganizeResponse(399, 'Sem erro');

        $this->assertFalse($response->getError(), 'Status 399 não deveria ser erro.');
    }

    public function testGetErrorReturnsTrueForStatus400(): void
    {
        $response = new OrganizeResponse(400, 'Requisição inválida');

        $this->assertTrue($response->getError(), 'Status 400 deveria ser erro.');
    }

    public function testGetErrorReturnsTrueForStatusAbove400(): void
    {
        $response = new OrganizeResponse(500, 'Erro interno');

        $this->assertTrue($response->getError(), 'Status 500 deveria ser erro.');
    }
}

