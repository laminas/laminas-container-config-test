<?php

declare(strict_types=1);

namespace LaminasTest\ContainerConfigTest\TestAsset;

use Laminas\ContainerConfigTest\TestAsset\Service;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    /**
     * @covers \Laminas\ContainerConfigTest\TestAsset\Service::inject
     */
    public function testInject(): void
    {
        $service = new Service();

        $service->inject('foo');
        self::assertCount(1, $service->injected);
        self::assertSame('foo', $service->injected[0]);

        $service->inject('bar');
        self::assertCount(2, $service->injected);
        self::assertSame('bar', $service->injected[1]);
    }
}
