<?php

namespace Transaction\Authorization;

use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Tests\TestCase;
use Transaction\Application\Authorization\Service;
use Transaction\Infra\Eloquent\Transaction;

class ServiceTest extends TestCase
{
    /**
     * @dataProvider getAuthorizationsScenarios
     */
    public function test_should_handle_the_authorization_service(string $message, bool $expected): void
    {
        // Set
        $client = m::mock(\Transaction\Infra\Client\Authorization::class);
        $service = new Service($client);
        $transaction = m::mock(Transaction::class);
        $response = m::mock(ResponseInterface::class);
        $body = m::mock(StreamInterface::class);

        // Expectations
        $client->expects()
            ->send($transaction)
            ->andReturn($response);

        $response->expects()
            ->getBody()
            ->andReturn($body);

        $body->expects()
            ->getContents()
            ->andReturn($message);

        // Actions
        $result = $service->handle($transaction);

        // Assertions
        $this->assertSame($expected, $result);
    }

    public function getAuthorizationsScenarios(): array
    {
        return [
            'authorized' => [
                'message' => '{"message":"Autorizado"}',
                'expected' => true,
            ],
            'not authorized' => [
                'message' => '{"message":"Nao Autorizado"}',
                'expected' => false,
            ],
        ];
    }
}
