<?php

namespace Transaction\Infra;

use MongoDB\BSON\ObjectId;

trait GenerateObjectId
{
    protected function getNumber(): string
    {
        return new ObjectId();
    }
}
