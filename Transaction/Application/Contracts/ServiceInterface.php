<?php

namespace Transaction\Application\Contracts;

interface ServiceInterface
{
    public function handle(InputBoundary $input): OutputBoundary;
}
