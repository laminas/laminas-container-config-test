<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Zend\ContainerConfigTest\Helper\Assert;

use function array_shift;

trait FactoryTestTrait
{
    /**
     * @dataProvider \Zend\ContainerConfigTest\Helper\Provider::factory
     */
    final public function testFactoryIsUsedToProduceService(array $config) : void
    {
        $container = $this->createContainer($config);

        self::assertTrue($container->has('service'));
        $service = $container->get('service');
        self::assertInstanceOf(TestAsset\Service::class, $service);
        self::assertSame($service, $container->get('service'));
    }

    /**
     * @dataProvider \Zend\ContainerConfigTest\Helper\Provider::factoryWithName
     */
    final public function testFactoryIsProvidedContainerAndServiceNameAsArguments(array $config) : void
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
     * @dataProvider \Zend\ContainerConfigTest\Helper\Provider::invalidFactory
     */
    final public function testInvalidFactoryResultsInExceptionDuringInstanceRetrieval(
        array $config,
        string $name,
        string $originName,
        array $expectedExceptions = []
    ) : void {
        $expectedExceptions[] = ContainerExceptionInterface::class;
        $container = $this->createContainer($config);

        self::assertTrue($container->has($name));
        Assert::expectedExceptions(
            function () use ($container, $name) {
                $container->get($name);
            },
            $expectedExceptions
        );
    }
}
