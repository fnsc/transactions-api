<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Laravel\Set\LaravelSetList;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // Define what rule sets will be applied
    $rectorConfig->import(LevelSetList::UP_TO_PHP_81);

    // Laravel 9
    $rectorConfig->sets([
        LaravelSetList::LARAVEL_90,
    ]);

    // get services (needed for register a single rule)
     $services = $rectorConfig->services();

    // register a single rule
     $services->set(TypedPropertyRector::class);
};
