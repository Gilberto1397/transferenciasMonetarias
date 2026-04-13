<?php

namespace App\Clients;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class AuthorizationClient
{
    const BASE_URL = 'https://util.devi.tools/api/v2/authorize';
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function checkAuthorization(): bool
    {
        /**
         * @var ResponseInterface $response
         */
        $response = $this->client->getAsync(self::BASE_URL, ['http_errors' => false])->wait();

        /**
         * @var object{status: string, data: object{authorization: bool}} $responseObject
         */
        $responseObject = json_decode($response->getBody()->getContents());

        if (($response->getStatusCode() !== 200 && $response->getStatusCode() !== 403) ||
            (!isset($responseObject->data->authorization) && !is_bool($responseObject->data->authorization))) { //@phpstan-ignore-line
            throw new \DomainException('Falha ao consultar autorização de transferência! Tente novamente.');
        }
        return $responseObject->data->authorization; //@phpstan-ignore-line
    }
}
