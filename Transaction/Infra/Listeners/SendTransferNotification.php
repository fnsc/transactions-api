<?php

namespace Transaction\Infra\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Transaction\Application\Contracts\Client;
use Transaction\Application\Events\TransferProcessed;
use Transaction\Application\Exceptions\TransferException;

class SendTransferNotification implements ShouldQueue
{
    use InteractsWithQueue;

    private const SUCCESS = 'Success';

    public string $connection = 'database';
    public int $tries = 3;

    public function __construct(private readonly Client $client)
    {
    }

    public function handle(TransferProcessed $event): void
    {
        $response = $this->client->send($event->getTransaction());
        $body = json_decode($response->getBody()->getContents(), true);

        if (self::SUCCESS !== $body['message']) {
            throw TransferException::notificationWasNotSend($response->getStatusCode());
        }
    }
}
