<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

use Psr\Container\ContainerExceptionInterface;
use Zend\ContainerConfigTest\Helper\Assert;

trait InvokableTestTrait
{
    final public function testCanSpecifyMultipleInvokablesWithoutKeyAndNotCauseCollisions() : void
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

        $instance = $container->get(TestAsset\Service::class);
        self::assertInstanceOf(TestAsset\Service::class, $instance);

        $instance = $container->get(TestAsset\DelegatorFactory::class);
        self::assertInstanceOf(TestAsset\DelegatorFactory::class, $instance);
    }

    /**
     * @dataProvider \Zend\ContainerConfigTest\Helper\Provider::invokable
     */
    final public function testInvokable(
        array $config,
        string $alias,
        string $name
    ) : void {
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
     * @dataProvider \Zend\ContainerConfigTest\Helper\Provider::invalidInvokable
     */
    final public function testInvalidInvokableResultsInExceptionDuringInstanceRetrieval(
        array $config,
        string $name,
        string $originName,
        array $expectedExceptions = []
    ) : void {
        $expectedExceptions[] = ContainerExceptionInterface::class;
        $container = $this->createContainer($config);

        self::assertTrue($container->has($name));
        Assert::expectedExceptions(
            function () use ($container) {
                $container->get(TestAsset\Delegator::class);
            },
            $expectedExceptions
        );
    }
}
