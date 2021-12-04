<?php

namespace Transfer\Listeners;

use Illuminate\Http\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Transfer\Client\NotificationClient;
use Transfer\Events\TransferProcessed;
use Transfer\TransferException;

class SendTransferNotificationTest extends TestCase
{
    public function test_should_send_the_transfer_notification(): void
    {
        // Set
        $event = m::mock(TransferProcessed::class);
        $client = m::mock(NotificationClient::class);
        $response = m::mock(ResponseInterface::class);
        $body = m::mock(StreamInterface::class);
        $listener = new SendTransferNotification($client);

        // Expectations
        $event->expects()
            ->getAttributes()
            ->andReturn([]);

        $client->expects()
            ->send(m::type('array'))
            ->andReturn($response);

        $response->expects()
            ->getBody()
            ->andReturn($body);

        $body->expects()
            ->getContents()
            ->andReturn('{"message":"Success"}');

        // Actions
        $listener->handle($event);

        // Assertions
        $this->assertNull(null);
    }

    public function test_should_throw_an_exception_when_notification_service_fails(): void
    {
        // Set
        $event = m::mock(TransferProcessed::class);
        $client = m::mock(NotificationClient::class);
        $response = m::mock(ResponseInterface::class);
        $body = m::mock(StreamInterface::class);
        $listener = new SendTransferNotification($client);

        // Expectations
        $event->expects()
            ->getAttributes()
            ->andReturn([]);

        $client->expects()
            ->send(m::type('array'))
            ->andReturn($response);

        $response->expects()
            ->getBody()
            ->andReturn($body);

        $response->expects()
            ->getStatusCode()
            ->andReturn(Response::HTTP_BAD_REQUEST);

        $body->expects()
            ->getContents()
            ->andReturn('{"message":"Fail"}');

        $this->expectExceptionMessage('The user notification was not send due an issue with the provider.');
        $this->expectException(TransferException::class);
        $this->expectExceptionCode(Response::HTTP_BAD_REQUEST);

        // Actions
        $listener->handle($event);
    }
}
