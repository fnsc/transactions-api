<?php

namespace Transaction\Application\Authorization;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Transaction\Domain\Entities\Transaction;
use Transaction\Infra\Client\Authorization as AuthorizationClient;

class ServiceTest extends TestCase
{
    /**
     * @dataProvider getAuthorizationsScenarios
     */
    public function test_should_handle_the_authorization_service(string $message, bool $expected): void
    {
        // Set
        $client = m::mock(AuthorizationClient::class);
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
