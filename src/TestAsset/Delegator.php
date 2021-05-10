<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

class Delegator
{
    public $callback;

    public function __construct($name, callable $callback)
    {
        $this->callback = $callback;
    }
}
