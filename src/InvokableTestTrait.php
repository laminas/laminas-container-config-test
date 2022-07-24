<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use Laminas\ContainerConfigTest\Helper\Assert;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

trait InvokableTestTrait
{
    final public function testCanSpecifyMultipleInvokablesWithoutKeyAndNotCauseCollisions(): void
    {
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

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::invalidInvokable
     * @param array<string,mixed> $config
     * @param list<class-string<Throwable>> $expectedExceptions
     */
    final public function testInvalidInvokableResultsInExceptionDuringInstanceRetrieval(
        array $config,
        string $name,
        string $originName,
        array $expectedExceptions = []
    ): void {
        $expectedExceptions[] = ContainerExceptionInterface::class;
        $container            = $this->createContainer($config);

        self::assertTrue($container->has($name));
        Assert::expectedExceptions(
            function () use ($container) {
                $container->get(TestAsset\Delegator::class);
            },
            $expectedExceptions
        );
    }
}
