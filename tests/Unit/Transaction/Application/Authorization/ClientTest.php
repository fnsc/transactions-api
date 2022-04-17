<?php

namespace Transaction\Authorization;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Config\Repository as Config;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;
use Transaction\Application\Authorization\TransactionTransformer;
use Transaction\Infra\Client\Authorization;
use Transaction\Infra\Eloquent\Transaction;

class ClientTest extends TestCase
{
    public function test(): void
    {
        // Set
        $httpClient = m::mock(HttpClient::class);
        $config = m::mock(Config::class);
        $transformer = m::mock(TransactionTransformer::class);
        $client = new Authorization($httpClient, $config, $transformer);
        $transaction = m::mock(Transaction::class);
        $transformedTransaction = ['transaction' => 'transformed'];
        $uri = 'https://some-uri.com';
        $options = [
            'header' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($transformedTransaction),
        ];

        // Expectations
        $config->expects()
            ->get('transaction.authorization.uri')
            ->andReturn($uri);

        $transformer->expects()
            ->transform($transaction)
            ->andReturn($transformedTransaction);

        $httpClient->expects()
            ->post($uri, $options)
            ->andReturn(m::mock(ResponseInterface::class));

        // Actions
        $client->send($transaction);

        // Assertions
    }
}
