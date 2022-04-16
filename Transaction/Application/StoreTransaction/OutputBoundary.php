<?php

namespace Transaction\Application\StoreTransaction;

use Transaction\Application\Contracts\OutputBoundary as OutputBoundaryInterface;

class OutputBoundary implements OutputBoundaryInterface
{
    public function __construct(
        private readonly string $message,
        private readonly array $data
    ) {
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
