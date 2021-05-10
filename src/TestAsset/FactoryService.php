<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

class FactoryService
{
    public $args = [];

    public function __construct(array $args)
    {
        $this->args = $args;
    }
}
