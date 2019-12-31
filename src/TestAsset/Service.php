<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

class Service
{
    public $injected = [];

    /**
     * @param mixed $a
     * @return mixed
     */
    public function __invoke($a = null)
    {
        return $a;
    }

    public function inject(string $name) : void
    {
        $this->injected[] = $name;
    }
}
