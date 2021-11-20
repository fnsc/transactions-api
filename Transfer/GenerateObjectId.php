<?php

namespace Transfer;

use MongoDB\BSON\ObjectId;

trait GenerateObjectId
{
    protected function getNumber(): string
    {
        return new ObjectId();
    }
}
