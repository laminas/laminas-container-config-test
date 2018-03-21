<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

use Generator;
use Psr\Container\ContainerExceptionInterface;
use Throwable;
use TypeError;

trait FactoryTestTrait
{
    public function factory() : Generator
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
    public function testFactoryIsUsedToProduceService(array $config) : void
    {
        $container = $this->createContainer($config);

        self::assertTrue($container->has('service'));
        $service = $container->get('service');
        self::assertInstanceOf(TestAsset\Service::class, $service);
        self::assertSame($service, $container->get('service'));
    }

    public function factoryWithName() : Generator
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
                        return func_get_args();
                    },
                ],
            ],
        ];
    }

    /**
     * @dataProvider factoryWithName
     */
    public function testFactoryIsProvidedContainerAndServiceNameAsArguments(array $config) : void
    {
        $container = $this->createContainer($config);

        $args = $container->get('service');
        self::assertGreaterThanOrEqual(2, $args);
        self::assertSame($container, array_shift($args));
        self::assertEquals('service', array_shift($args));
    }

    public function testFactoryReferencingAServiceWillResultInExceptionDuringRetrieval() : void
    {
        $container = $this->createContainer([
            'factories' => ['service' => 'factory'],
            'services' => ['factory' => new TestAsset\Factory()],
        ]);

        self::assertTrue($container->has('service'));

        $this->expectException(ContainerExceptionInterface::class);
        $container->get('service');
    }

    public function testNonInvokableFactoryClassNameResultsInExceptionDuringInstanceRetrieval()
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

    public function testNonExistentFactoryClassResultsInExceptionDuringInstanceRetrieval()
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

    public function testFactoryClassNameRequiringConstructorArgumentsResultsInExceptionDuringInstanceRetrieval()
    {
        $container = $this->createContainer([
            'factories' => [
                'service' => TestAsset\FactoryWithRequiredParameters::class,
            ],
        ]);

        self::assertTrue($container->has('service'));

        $caught = false;
        try {
            $container->get('service');
        } catch (Throwable $e) {
            if ($e instanceof TypeError || $e instanceof ContainerExceptionInterface) {
                $caught = true;
            }
        }

        $this->assertTrue($caught, 'No TypeError or ContainerExceptionInterface thrown when one was expected');
    }
}
