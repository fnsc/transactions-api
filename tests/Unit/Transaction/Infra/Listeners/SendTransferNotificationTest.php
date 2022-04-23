<?php

namespace Transaction\Infra\Listeners;

use Illuminate\Http\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Transaction\Application\Events\TransferProcessed;
use Transaction\Application\Exceptions\TransferException;
use Transaction\Domain\Entities\Transaction;
use Transaction\Infra\Client\Notification;

class SendTransferNotificationTest extends TestCase
{
    public function testShouldSendTheTransferNotification(): void
    {
        // Set
        $client = m::mock(Notification::class);
        $listener = new SendTransferNotification($client);

        $event = m::mock(TransferProcessed::class);
        $response = m::mock(ResponseInterface::class);
        $body = m::mock(StreamInterface::class);
        $transaction = m::mock(Transaction::class);

        // Expectations
        $event->expects()
            ->getTransaction()
            ->andReturn($transaction);

        $client->expects()
            ->send($transaction)
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

    public function testShouldThrowAnExceptionWhenNotificationServiceFails(): void
    {
        // Set
        $client = m::mock(Notification::class);
        $listener = new SendTransferNotification($client);

        $event = m::mock(TransferProcessed::class);
        $response = m::mock(ResponseInterface::class);
        $body = m::mock(StreamInterface::class);
        $transaction = m::mock(Transaction::class);

        // Expectations
        $event->expects()
            ->getTransaction()
            ->andReturn($transaction);

        $client->expects()
            ->send($transaction)
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
