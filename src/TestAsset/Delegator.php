<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

class Delegator
{
    /** @var callable */
    public $callback;

    /**
     * @param mixed $name
     */
    public function __construct($name, callable $callback)
    {
        $this->callback = $callback;
    }
}
