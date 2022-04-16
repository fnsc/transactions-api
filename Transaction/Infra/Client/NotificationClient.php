<?php

namespace Transaction\Infra\Client;

use GuzzleHttp\Client;
use Illuminate\Config\Repository as Config;
use Psr\Http\Message\ResponseInterface;

class NotificationClient
{
    private Client $client;
    private Config $config;

    public function __construct(Client $client, Config $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function send(array $transaction): ResponseInterface
    {
        $uri = $this->config->get('transaction.notification.uri');
        $options['header'] = $this->getContentType();
        $options['body'] = json_encode($transaction);

        return $this->client->post($uri, $options);
    }

    private function getContentType(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
