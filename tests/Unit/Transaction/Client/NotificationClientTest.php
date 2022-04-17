<?php

namespace Transaction\Client;

use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Transaction\Infra\Client\Notification;

class NotificationClientTest extends TestCase
{
    public function test_should_send_the_transfer_notification(): void
    {
        // Set
        $client = m::mock(Client::class);
        $config = m::mock(Repository::class);
        $notificationClient = new Notification($client, $config);
        $transaction = ['transaction' => 'data'];
        $uri = 'https://some-url.com';
        $options = [
            'header' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($transaction),
        ];

        // Expectations
        $config->expects()
            ->get('transaction.notification.uri')
            ->andReturn($uri);

        $client->expects()
            ->post($uri, $options)
            ->andReturn(m::mock(ResponseInterface::class));

        // Actions
        $result = $notificationClient->send($transaction);

        // Assertions
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
