<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

use ArgumentCountError;
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

    final public function testFactoryReferencingAServiceWillResultInExceptionDuringRetrieval() : void
    {
        $container = $this->createContainer([
            'factories' => ['service' => 'factory'],
            'services' => ['factory' => new TestAsset\Factory()],
        ]);

        self::assertTrue($container->has('service'));

        $this->expectException(ContainerExceptionInterface::class);
        $container->get('service');
    }

    final public function testNonInvokableFactoryClassNameResultsInExceptionDuringInstanceRetrieval() : void
    {
        $container = $this->createContainer([
            'factories' => [
                'service' => TestAsset\NonInvokableFactory::class,
            ],
        ]);

        self::assertTrue($container->has('service'));
        $this->expectException(ContainerExceptionInterface::class);
        $container->get('service');
    }

    final public function testNonExistentFactoryClassResultsInExceptionDuringInstanceRetrieval() : void
    {
        $container = $this->createContainer([
            'factories' => [
                'service' => TestAsset\NonExistentFactory::class,
            ],
        ]);

        self::assertTrue($container->has('service'));
        $this->expectException(ContainerExceptionInterface::class);
        $container->get('service');
    }

    final public function testFactoryConstructorRequiringArgumentsResultsInExceptionDuringInstanceRetrieval() : void
    {
        $container = $this->createContainer([
            'factories' => [
                'service' => TestAsset\FactoryWithRequiredParameters::class,
            ],
        ]);

        self::assertTrue($container->has('service'));

        Assert::expectedExceptions(
            function () use ($container) {
                $container->get('service');
            },
            [ArgumentCountError::class, ContainerExceptionInterface::class]
        );
    }
}
