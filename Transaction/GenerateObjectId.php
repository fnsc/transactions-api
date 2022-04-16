<?php

namespace Transaction;

use MongoDB\BSON\ObjectId;

trait GenerateObjectId
{
    protected function getNumber(): string
    {
        return new ObjectId();
    }
}
