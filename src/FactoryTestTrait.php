<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use Laminas\ContainerConfigTest\Helper\Provider;
use Laminas\ContainerConfigTest\TestAsset\FactoryService;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Psr\Container\ContainerInterface;

use function array_shift;
use function assert;

/**
 * @psalm-require-extends AbstractContainerTest
 */
trait FactoryTestTrait
{
    /**
     * @param array<string,mixed> $config
     */
    #[DataProviderExternal(Provider::class, 'factory')]
    final public function testFactoryIsUsedToProduceService(array $config): void
    {
        assert($this instanceof AbstractContainerTest);
        $container = $this->createContainer($config);

        self::assertTrue($container->has('service'));
        $service = $container->get('service');
        self::assertInstanceOf(TestAsset\Service::class, $service);
        self::assertSame($service, $container->get('service'));
    }

    /**
     * @param array<string,mixed> $config
     */
    #[DataProviderExternal(Provider::class, 'factoryWithName')]
    final public function testFactoryIsProvidedContainerAndServiceNameAsArguments(array $config): void
    {
        assert($this instanceof AbstractContainerTest);
        $container = $this->createContainer($config);

        $service = $container->get('service');
        self::assertInstanceOf(FactoryService::class, $service);
        $args = $service->args;
        self::assertGreaterThanOrEqual(2, $args);
        // Not testing for identical $container argument here, as some implementations
        // may decorate another container in order to fulfill the config contracts.
        self::assertInstanceOf(ContainerInterface::class, array_shift($args));
        self::assertEquals('service', array_shift($args));
    }
}
