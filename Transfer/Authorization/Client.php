<?php

namespace Transfer\Authorization;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Config\Repository as Config;
use Psr\Http\Message\ResponseInterface;
use Transfer\Transaction;

class Client
{
    private HttpClient $client;
    private Config $config;
    private TransactionTransformer $transformer;

    public function __construct(HttpClient $client, Config $config, TransactionTransformer $transformer)
    {
        $this->client = $client;
        $this->config = $config;
        $this->transformer = $transformer;
    }

    public function send(Transaction $transaction): ResponseInterface
    {
        $uri = $this->config->get('transaction.authorization.uri');
        $options = $this->getOptions($transaction);

        return $this->client->post($uri, $options);
    }

    private function getOptions(Transaction $transaction): array
    {
        $options['header'] = $this->getHeader();
        $options['body'] = json_encode($this->transformer->transform($transaction));

        return $options;
    }

    private function getHeader(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
