<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\TestAsset;

class Service
{
    public array $injected = [];

    /**
     * @param mixed $a
     * @return mixed
     */
    public function __invoke($a = null)
    {
        return $a;
    }

    public function inject(string $name): void
    {
        $this->injected[] = $name;
    }
}
