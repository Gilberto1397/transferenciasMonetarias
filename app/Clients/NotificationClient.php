<?php

namespace App\Clients;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class NotificationClient
{
    const BASE_URL = 'https://util.devi.tools/api/v1/notify';
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function throwNotification(): bool
    {
        /**
         * @var ResponseInterface $response
         */
        $response = $this->client->postAsync(self::BASE_URL, ['http_errors' => false])->wait();

        if ($response->getStatusCode() !== 204 && $response->getStatusCode() !== 504) {
            throw new \DomainException('Falha ao notificar transferência! Tente novamente.');
        }

        /**
         * @var string $responseObject
         */
        $responseObject = $response->getBody()->getContents();

        if ($responseObject !== '') {
            /**
             * @var object{status: string, message: string}|null $responseObject
             */
            $responseObject = json_decode($responseObject);
        } else {
            $responseObject = null;
        }

        if ($responseObject !== null &&
            $response->getStatusCode() === 504 &&
            !empty($responseObject->status) &&
            $responseObject->status === 'error') {
            return false;
        }

        if ($response->getStatusCode() === 204 && $responseObject === null) {
            return true;
        }
        throw new \DomainException('Falha no serviço de notificação! Tente novamente.');
    }
}
