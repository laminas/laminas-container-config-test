<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use Laminas\ContainerConfigTest\Helper\Assert;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function array_shift;

trait FactoryTestTrait
{
    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::factory
     * @param array<string,mixed> $config
     */
    final public function testFactoryIsUsedToProduceService(array $config): void
    {
        $container = $this->createContainer($config);

        self::assertTrue($container->has('service'));
        $service = $container->get('service');
        self::assertInstanceOf(TestAsset\Service::class, $service);
        self::assertSame($service, $container->get('service'));
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::factoryWithName
     * @param array<string,mixed> $config
     */
    final public function testFactoryIsProvidedContainerAndServiceNameAsArguments(array $config): void
    {
        $container = $this->createContainer($config);

        $args = $container->get('service')->args;
        self::assertGreaterThanOrEqual(2, $args);
        // Not testing for identical $container argument here, as some implementations
        // may decorate another container in order to fulfill the config contracts.
        self::assertInstanceOf(ContainerInterface::class, array_shift($args));
        self::assertEquals('service', array_shift($args));
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::invalidFactory
     * @param array<string,mixed> $config
     */
    final public function testInvalidFactoryResultsInExceptionDuringInstanceRetrieval(
        array $config,
        string $name,
        string $originName,
        array $expectedExceptions = []
    ): void {
        $expectedExceptions[] = ContainerExceptionInterface::class;
        $container            = $this->createContainer($config);

        self::assertTrue($container->has($name));
        Assert::expectedExceptions(
            function () use ($container, $name) {
                $container->get($name);
            },
            $expectedExceptions
        );
    }
}
