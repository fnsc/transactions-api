<?php

namespace Transfer\Authorization;

use Psr\Http\Message\ResponseInterface;
use Transfer\Transaction;

class Service
{
    private const AUTHORIZED = 'Autorizado';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle(Transaction $transaction): bool
    {
        $response = $this->client->send($transaction);

        return $this->isAuthorized($response);
    }

    private function isAuthorized(ResponseInterface $response): bool
    {
        $body = json_decode($response->getBody()->getContents(), true);

        if (self::AUTHORIZED !== $body['message']) {
            return false;
        }

        return true;
    }
}
