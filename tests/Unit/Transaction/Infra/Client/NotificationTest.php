<?php

namespace Transaction\Infra\Client;

use DateTime;
use GuzzleHttp\Client;
use Illuminate\Config\Repository as Config;
use Mockery as m;
use Money\Money;
use Money\MoneyFormatter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Transaction\Domain\Entities\Transaction as TransactionEntity;
use Transaction\Domain\Entities\User as UserEntity;

class NotificationTest extends TestCase
{
    public function test_should_send_a_request_to_get_authorization(): void
    {
        // Set
        $http = m::mock(Client::class);
        $config = m::mock(Config::class);
        $formatter = $this->createMock(MoneyFormatter::class);
        $client = new Notification($http, $config, $formatter);
        $transaction = m::mock(TransactionEntity::class);
        $user = m::mock(UserEntity::class);
        $uri = 'http://notification.service';
        $options = [
            'header' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode([
                'payer' => 'Payer Name',
                'payee' => 'Payer Name',
                'amount' => 'R$1,00',
                'created_at' => (new DateTime())->format(DATE_ATOM),
            ]),
        ];

        // Expectations
        $config->expects()
            ->get('transaction.notification.uri')
            ->andReturn($uri);

        $transaction->expects()
            ->getPayer()
            ->andReturn($user);

        $user->expects()
            ->getName()
            ->andReturn('Payer Name');

        $transaction->expects()
            ->getPayee()
            ->andReturn($user);

        $transaction->expects()
            ->getAmount()
            ->andReturn(Money::BRL(100));

        $formatter->expects($this->once())
            ->method('format')
            ->with(Money::BRL(100))
            ->willReturn('R$1,00');

        $http->expects()
            ->post($uri, $options)
            ->andReturn(m::mock(ResponseInterface::class));

        // Actions
        $result = $client->send($transaction);

        // Assertions
        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
