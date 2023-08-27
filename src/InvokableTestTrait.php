<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use function assert;

/**
 * @psalm-require-extends AbstractContainerTest
 */
trait InvokableTestTrait
{
    final public function testCanSpecifyMultipleInvokablesWithoutKeyAndNotCauseCollisions(): void
    {
        assert($this instanceof AbstractContainerTest);
        $config = [
            'invokables' => [
                TestAsset\Service::class,
                TestAsset\DelegatorFactory::class,
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has(TestAsset\Service::class));
        self::assertTrue($container->has(TestAsset\DelegatorFactory::class));

        /** @var TestAsset\Service $instance */
        $instance = $container->get(TestAsset\Service::class);
        self::assertInstanceOf(TestAsset\Service::class, $instance);

        /** @var TestAsset\DelegatorFactory $instance */
        $instance = $container->get(TestAsset\DelegatorFactory::class);
        self::assertInstanceOf(TestAsset\DelegatorFactory::class, $instance);
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::invokable
     * @param array<string,mixed> $config
     */
    final public function testInvokable(
        array $config,
        string $alias,
        string $name
    ): void {
        assert($this instanceof AbstractContainerTest);
        $container = $this->createContainer($config);

        self::assertTrue($container->has($alias));
        $service = $container->get($alias);
        self::assertInstanceOf(TestAsset\Service::class, $service);

        self::assertTrue($container->has($name));
        $originService = $container->get($name);
        self::assertInstanceOf(TestAsset\Service::class, $originService);

        self::assertSame($service, $originService);
        self::assertSame($originService, $container->get($name));
    }
}
