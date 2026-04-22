<?php

namespace Tests\Unit\Clients;

use App\Clients\AuthorizationClient;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

class AuthorizationClientTest extends TestCase
{
    public function testCheckAuthorizationReturnsTrueWhenStatusIs200AndAuthorizationIsTrue(): void
    {
        /**
         * Given - Arrange
         */
        $clientMock = $this->makeClientMock(200, '{"status":"success","data":{"authorization":true}}');
        $authorizationClient = new AuthorizationClient($clientMock);

        /**
         * When - Act
         */
        $response = $authorizationClient->checkAuthorization();

        /**
         * Then - Assert
         */
        $this->assertTrue($response, 'A autorização deveria ser true para status 200.');
    }

    public function testCheckAuthorizationReturnsFalseWhenStatusIs403AndAuthorizationIsFalse(): void
    {
        /**
         * Given - Arrange
         */
        $clientMock = $this->makeClientMock(403, '{"status":"success","data":{"authorization":false}}');
        $authorizationClient = new AuthorizationClient($clientMock);

        /**
         * When - Act
         */
        $response = $authorizationClient->checkAuthorization();

        /**
         * Then - Assert
         */
        $this->assertFalse($response, 'A autorização deveria ser false para status 403.');
    }

    public function testCheckAuthorizationThrowsDomainExceptionWhenStatusCodeIsInvalid(): void
    {
        /**
         * Given - Arrange
         */
        $clientMock = $this->makeClientMock(500, '{"status":"error","data":{"authorization":false}}');
        $authorizationClient = new AuthorizationClient($clientMock);

        /**
         * Then - Assert
         */
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Falha ao consultar autorização de transferência! Tente novamente.');

        /**
         * When - Act
         */
        $authorizationClient->checkAuthorization();
    }

    public function testCheckAuthorizationThrowsDomainExceptionWhenAuthorizationFieldIsMissing(): void
    {
        /**
         * Given - Arrange
         */
        $clientMock = $this->makeClientMock(200, '{"status":"success","data":{"authorization":null}}');
        $authorizationClient = new AuthorizationClient($clientMock);

        /**
         * Then - Assert
         */
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Falha ao consultar autorização de transferência! Tente novamente.');

        /**
         * When - Act
         */
        $authorizationClient->checkAuthorization();
    }

    private function makeClientMock(int $statusCode, string $body): Client
    {
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->expects($this->once())
            ->method('getContents')
            ->willReturn($body);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn($statusCode);
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);

        $promiseMock = $this->createMock(PromiseInterface::class);
        $promiseMock->expects($this->once())
            ->method('wait')
            ->willReturn($responseMock);

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('getAsync')
            ->with(AuthorizationClient::BASE_URL, ['http_errors' => false])
            ->willReturn($promiseMock);

        return $clientMock;
    }
}

