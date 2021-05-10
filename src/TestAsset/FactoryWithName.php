<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

class FactoryWithName
{
    public function __invoke() : FactoryService
    {
        return new FactoryService(func_get_args());
    }
}
