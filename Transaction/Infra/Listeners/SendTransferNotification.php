<?php

namespace Transaction\Infra\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Transaction\Application\Events\TransferProcessed;
use Transaction\Infra\Client\NotificationClient;
use Transaction\TransferException;

class SendTransferNotification implements ShouldQueue
{
    use InteractsWithQueue;

    private const SEND = 'Success';

    public string $connection = 'database';
    public int $tries = 3;
    private NotificationClient $client;

    public function __construct(NotificationClient $client)
    {
        $this->client = $client;
    }

    public function handle(TransferProcessed $event): void
    {
        $response = $this->client->send($event->getAttributes());
        $body = json_decode($response->getBody()->getContents(), true);

        if (self::SEND !== $body['message']) {
            throw TransferException::notificationWasNotSend($response->getStatusCode());
        }
    }
}
