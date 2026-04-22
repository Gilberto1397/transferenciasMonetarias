<?php

namespace Tests\Unit\Clients;

use App\Clients\NotificationClient;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;

class NotificationClientTest extends TestCase
{
    public function testThrowNotificationReturnsTrueWhenStatusIs204AndBodyIsEmpty(): void
    {
        /**
         * Given - Arrange
         */
        $clientMock = $this->makeClientMock(204, '');
        $notificationClient = new NotificationClient($clientMock);

        /**
         * When - Act
         */
        $response = $notificationClient->throwNotification();

        /**
         * Then - Assert
         */
        $this->assertTrue($response, 'A notificação deveria retornar true para status 204 e corpo vazio.');
    }

    public function testThrowNotificationReturnsFalseWhenStatusCodeIs504AndStatusError(): void
    {
        /**
         * Given - Arrange
         */
        $clientMock = $this->makeClientMock(504, '{"status":"error","message":"Gateway timeout"}');
        $notificationClient = new NotificationClient($clientMock);

        /**
         * When - Act
         */
        $response = $notificationClient->throwNotification();

        /**
         * Then - Assert
         */
        $this->assertFalse($response, 'A notificação deveria retornar false para status 504 com status error.');
    }

    /**
     * @dataProvider invalidStatusCodes
     * @return void
     */
    public function testThrowNotificationThrowsDomainExceptionWhenStatusCodeIsInvalid(int $statusCode): void
    {
        /**
         * Given - Arrange
         */
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->expects($this->never())
            ->method('getContents')
            ->willReturn('{"status":"error","message":"Internal server error"}');

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getStatusCode')->willReturn($statusCode);
        $responseMock->expects($this->never())
            ->method('getBody')
            ->willReturn($streamMock);

        $promiseMock = $this->createMock(PromiseInterface::class);
        $promiseMock->expects($this->once())
            ->method('wait')
            ->willReturn($responseMock);

        $clientMock = $this->createMock(Client::class);
        $clientMock->expects($this->once())
            ->method('postAsync')
            ->with(NotificationClient::BASE_URL, ['http_errors' => false])
            ->willReturn($promiseMock);

        $notificationClient = new NotificationClient($clientMock);

        /**
         * Then - Assert
         */
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Falha ao notificar transferência! Tente novamente.');

        /**
         * When - Act
         */
        $notificationClient->throwNotification();
    }

    public function testThrowNotificationThrowsDomainExceptionWhenStatusCodeIs204AndBodyIsNotNull(): void
    {
        /**
         * Given - Arrange
         */
        $clientMock = $this->makeClientMock(204, '{"status":"success","message":"OK"}');
        $notificationClient = new NotificationClient($clientMock);

        /**
         * Then - Assert
         */
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Falha no serviço de notificação! Tente novamente.');

        /**
         * When - Act
         */
        $notificationClient->throwNotification();
    }

    public function testThrowNotificationThrowsDomainExceptionWhenStatusCodeIs504AndBodyIsEmpty(): void
    {
        /**
         * Given - Arrange
         */
        $clientMock = $this->makeClientMock(504, '');
        $notificationClient = new NotificationClient($clientMock);

        /**
         * Then - Assert
         */
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Falha no serviço de notificação! Tente novamente.');

        /**
         * When - Act
         */
        $notificationClient->throwNotification();
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
            ->method('postAsync')
            ->with(NotificationClient::BASE_URL, ['http_errors' => false])
            ->willReturn($promiseMock);

        return $clientMock;
    }

    public function invalidStatusCodes()
    {
        return [[500], [201], [306], [200], [404]];
    }
}

