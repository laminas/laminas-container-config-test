<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use ArgumentCountError;
use Generator;
use Laminas\ContainerConfigTest\Helper\Assert;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

trait FactoryTestTrait
{
    final public function factory() : Generator
    {
        yield 'function-name'        => [['factories' => ['service' => __NAMESPACE__ . '\TestAsset\factory']]];
        yield 'invokable-class-name' => [['factories' => ['service' => TestAsset\Factory::class]]];
        yield 'invokable-instance'   => [['factories' => ['service' => new TestAsset\Factory()]]];
        yield 'callable-array'       => [['factories' => ['service' => [TestAsset\FactoryStatic::class, 'create']]]];
        yield 'callable-string'      => [['factories' => ['service' => TestAsset\FactoryStatic::class . '::create']]];
        yield 'closure'   => [
            [
                'factories' => [
                    'service' => function () {
                        return new TestAsset\Service();
                    },
                ],
            ],
        ];
    }

    /**
     * @dataProvider factory
     */
    final public function testFactoryIsUsedToProduceService(array $config) : void
    {
        $container = $this->createContainer($config);

        self::assertTrue($container->has('service'));
        $service = $container->get('service');
        self::assertInstanceOf(TestAsset\Service::class, $service);
        self::assertSame($service, $container->get('service'));
    }

    final public function factoryWithName() : Generator
    {
        yield 'function-name'        => [['factories' => ['service' => __NAMESPACE__ . '\TestAsset\factoryWithName']]];
        yield 'invokable-class-name' => [['factories' => ['service' => TestAsset\FactoryWithName::class]]];
        yield 'invokable-instance'   => [['factories' => ['service' => new TestAsset\FactoryWithName()]]];
        yield 'callable-array'       => [['factories' => ['service' => [TestAsset\FactoryStatic::class, 'withName']]]];
        yield 'callable-string'      => [['factories' => ['service' => TestAsset\FactoryStatic::class . '::withName']]];
        yield 'closure' => [
            [
                'factories' => [
                    'service' => function () {
                        return new TestAsset\FactoryService(func_get_args());
                    },
                ],
            ],
        ];
    }

    /**
     * @dataProvider factoryWithName
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
