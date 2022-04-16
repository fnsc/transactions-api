<?php

namespace Transaction\Application\Authorization;

use Psr\Http\Message\ResponseInterface;
use Transaction\Domain\Entities\Transaction;

class Service
{
    private const AUTHORIZED = 'Autorizado';

    public function __construct(private readonly Client $client)
    {
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
