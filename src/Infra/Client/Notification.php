<?php

namespace Transaction\Infra\Client;

use GuzzleHttp\Client;
use Illuminate\Config\Repository as Config;
use Money\MoneyFormatter;
use Psr\Http\Message\ResponseInterface;
use Transaction\Application\Contracts\Client as ClientInterface;
use Transaction\Domain\Entities\Transaction;

class Notification extends AbstractClient implements ClientInterface
{
    public function __construct(
        private readonly Client $client,
        private readonly Config $config,
        private readonly MoneyFormatter $moneyFormatter
    ) {
        parent::__construct($this->moneyFormatter);
    }

    public function send(Transaction $transaction): ResponseInterface
    {
        $uri = $this->config->get('transaction.notification.uri');
        $options = $this->getOptions($transaction);

        return $this->client->post($uri, $options);
    }
}
