<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

use ArgumentCountError;
use Error;
use Generator;
use Psr\Container\ContainerExceptionInterface;
use Zend\ContainerConfigTest\Helper\Assert;

trait DelegatorTestTrait
{
    final public function testDelegatorsOperateOnInvokables() : void
    {
        $config = [
            'invokables' => [
                TestAsset\Service::class => TestAsset\Service::class,
            ],
            'delegators' => [
                TestAsset\Service::class => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has(TestAsset\Service::class));
        $instance = $container->get(TestAsset\Service::class);
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
        self::assertInstanceOf(TestAsset\Service::class, ($instance->callback)());

        // Retrieving a second time should retrieve the same instance.
        self::assertSame($instance, $container->get(TestAsset\Service::class));
    }

    final public function testDelegatorsDoNotOperateOnServices() : void
    {
        $myService = new TestAsset\Service();
        $config = [
            'services' => [
                'foo-bar' => $myService,
            ],
            'delegators' => [
                'foo-bar' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        $instance = $container->get('foo-bar');
        self::assertNotInstanceOf(TestAsset\Delegator::class, $instance);
        self::assertSame($myService, $instance);
    }

    final public function testDelegatorsApplyToInvokableServiceResolvedViaAlias() : void
    {
        $config = [
            'aliases' => [
                'alias' => TestAsset\Service::class,
            ],
            'invokables' => [
                TestAsset\Service::class => TestAsset\Service::class,
            ],
            'delegators' => [
                TestAsset\Service::class => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        $instance = $container->get('alias');
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
        self::assertInstanceOf(TestAsset\Service::class, ($instance->callback)());

        // Now ensure that the service fetched by alias is the same as that
        // fetched by the canonical service name.
        self::assertSame($instance, $container->get(TestAsset\Service::class));
    }

    final public function testDelegatorsNamedForAliasDoNotApplyToInvokableServiceResolvedViaAlias() : void
    {
        $config = [
            'aliases' => [
                'alias' => TestAsset\Service::class,
            ],
            'invokables' => [
                TestAsset\Service::class => TestAsset\Service::class,
            ],
            'delegators' => [
                'alias' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        $instance = $container->get('alias');
        self::assertInstanceOf(TestAsset\Service::class, $instance);
        self::assertNotInstanceOf(TestAsset\Delegator::class, $instance);

        // Now ensure that the instance already retrieved by alias is the same
        // as that when fetched by the canonical service name.
        self::assertSame($instance, $container->get(TestAsset\Service::class));
    }

    final public function testDelegatorsNamedForAliasDoNotApplyToInvokableServiceWithAlias() : void
    {
        $config = [
            'invokables' => [
                'alias' => TestAsset\Service::class,
            ],
            'delegators' => [
                'alias' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        $instance = $container->get('alias');
        self::assertInstanceOf(TestAsset\Service::class, $instance);
        self::assertNotInstanceOf(TestAsset\Delegator::class, $instance);

        // Now ensure that the instance already retrieved by alias is the same
        // as that when fetched by the canonical service name.
        self::assertSame($instance, $container->get(TestAsset\Service::class));
    }

    final public function testDelegatorsDoNotApplyToAliasResolvingToServiceEntry() : void
    {
        $myService = new TestAsset\Service();
        $config = [
            'aliases' => [
                'alias' => 'foo-bar',
            ],
            'services' => [
                'foo-bar' => $myService,
            ],
            'delegators' => [
                'alias' => [
                    TestAsset\DelegatorFactory::class,
                ],
                'foo-bar' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        $instance = $container->get('alias');
        self::assertNotInstanceOf(TestAsset\Delegator::class, $instance);
        self::assertSame($myService, $instance);

        // Now ensure that the instance already retrieved by alias is the same
        // as that when fetched by the canonical service name.
        self::assertSame($instance, $container->get('foo-bar'));
    }

    final public function testDelegatorsDoNotTriggerForAliasTargetingInvokableService() : void
    {
        $config = [
            'aliases' => [
                'alias' => TestAsset\Service::class,
            ],
            'invokables' => [
                TestAsset\Service::class,
            ],
            'delegators' => [
                'alias' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        $instance = $container->get('alias');
        self::assertInstanceOf(TestAsset\Service::class, $instance);
        self::assertNotInstanceOf(TestAsset\Delegator::class, $instance);

        // Now ensure that the instance already retrieved by alias is the same
        // as that when fetched by the canonical service name.
        self::assertSame($instance, $container->get(TestAsset\Service::class));
    }

    final public function delegatorService() : Generator
    {
        yield 'invokable' => [
            [
                'invokables' => [TestAsset\Service::class => TestAsset\Service::class],
            ],
            TestAsset\Service::class,
            TestAsset\Service::class,
        ];

        yield 'aliased-invokable' => [
            [
                'invokables' => ['foo-bar' => TestAsset\Service::class],
            ],
            'foo-bar',
            TestAsset\Service::class,
        ];

        yield 'alias-of-invokable' => [
            [
                'aliases' => ['foo-bar' => TestAsset\Service::class],
                'invokables' => [TestAsset\Service::class => TestAsset\Service::class],
            ],
            'foo-bar',
            TestAsset\Service::class,
        ];

        foreach ($this->factoriesForDelegators() as $name => $params) {
            yield 'factory-service-' . $name => [
                $params[0],
                'service',
                'service',
            ];

            yield 'alias-of-factory-service-' . $name => [
                $params[0] + ['aliases' => ['alias' => 'service']],
                'alias',
                'service',
            ];
        }
    }

    /**
     * @dataProvider delegatorService
     */
    final public function testDelegatorsReceiveCallbackResolvingToReturnValueOfPrevious(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ) : void {
        $config += [
            'delegators' => [
                $delegatedServiceName => [
                    TestAsset\Delegator1Factory::class,
                    TestAsset\Delegator2Factory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has($serviceNameToTest));
        $instance = $container->get($serviceNameToTest);
        self::assertInstanceOf(TestAsset\Service::class, $instance);
        self::assertEquals(
            [
                TestAsset\Delegator1Factory::class,
                TestAsset\Delegator2Factory::class,
            ],
            $instance->injected
        );

        // Ensure subsequent retrievals get same instance
        self::assertSame($instance, $container->get($serviceNameToTest));
    }

    /**
     * @dataProvider delegatorService
     */
    final public function testEmptyDelegatorListOriginalServiceShouldBeReturned(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ) : void {
        $config += [
            'delegators' => [
                $delegatedServiceName => [],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has($serviceNameToTest));
        $instance = $container->get($serviceNameToTest);
        self::assertInstanceOf(TestAsset\Service::class, $instance);
        self::assertEquals([], $instance->injected);

        // Ensure subsequent retrievals get same instance
        self::assertSame($instance, $container->get($serviceNameToTest));
    }

    final public function testMultipleAliasesForADelegatedInvokableServiceReceiveSameInstance() : void
    {
        $container = $this->createContainer([
            'invokables' => [
                'alias1' => TestAsset\Service::class,
                'alias2' => TestAsset\Service::class,
            ],
            'delegators' => [
                TestAsset\Service::class => [
                    TestAsset\Delegator1Factory::class,
                    TestAsset\Delegator2Factory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has('alias1'));
        self::assertTrue($container->has('alias2'));

        $instance = $container->get('alias1');
        self::assertInstanceOf(TestAsset\Service::class, $instance);
        self::assertEquals(
            [
                TestAsset\Delegator1Factory::class,
                TestAsset\Delegator2Factory::class,
            ],
            $instance->injected
        );

        // Ensure subsequent retrievals get same instance
        self::assertSame($instance, $container->get('alias1'));
        self::assertSame($instance, $container->get('alias2'));
        self::assertSame($instance, $container->get(TestAsset\Service::class));
    }

    /**
     * @dataProvider delegatorService
     */
    final public function testNonInvokableDelegatorClassNameResultsInExceptionDuringInstanceRetrieval(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ) : void {
        $container = $this->createContainer($config + [
            'delegators' => [
                $delegatedServiceName => [
                    TestAsset\NonInvokableFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has($serviceNameToTest));
        $this->expectException(ContainerExceptionInterface::class);
        $container->get($serviceNameToTest);
    }

    /**
     * @dataProvider delegatorService
     */
    final public function testNonExistentDelegatorClassResultsInExceptionDuringInstanceRetrieval(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ) : void {
        $container = $this->createContainer($config + [
            'delegators' => [
                $delegatedServiceName => [
                    TestAsset\NonExistentDelegatorFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has($serviceNameToTest));
        $this->expectException(ContainerExceptionInterface::class);
        $container->get($serviceNameToTest);
    }

    /**
     * @dataProvider delegatorService
     */
    final public function testDelegatorClassNameRequiringConstructorArgumentsResultsInExceptionDuringInstanceRetrieval(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ) : void {
        $container = $this->createContainer($config + [
            'delegators' => [
                $delegatedServiceName => [
                    TestAsset\FactoryWithRequiredParameters::class,
                ],
            ],
        ]);

        self::assertTrue($container->has($serviceNameToTest));

        Assert::expectedExceptions(
            function () use ($container, $serviceNameToTest) {
                $container->get($serviceNameToTest);
            },
            [ArgumentCountError::class, ContainerExceptionInterface::class]
        );
    }

    final public function factoriesForDelegators() : Generator
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
     * @dataProvider factoriesForDelegators
     */
    final public function testDelegatorFactoriesTriggerForFactoryBackedServicesUsingAnyFactoryType(array $config) : void
    {
        $config += [
            'delegators' => [
                'service' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('service'));
        $instance = $container->get('service');
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
        self::assertInstanceOf(TestAsset\Service::class, ($instance->callback)());

        // Retrieving a second time should retrieve the same instance.
        self::assertSame($instance, $container->get('service'));
    }

    /**
     * @dataProvider factoriesForDelegators
     */
    final public function testDelegatorsTriggerForFactoryServiceResolvedByAlias(array $config) : void
    {
        $config += [
            'aliases' => [
                'alias' => 'service',
            ],
            'delegators' => [
                'service' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        $instance = $container->get('alias');
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
        self::assertInstanceOf(TestAsset\Service::class, ($instance->callback)());

        // Now ensure that the instance already retrieved by alias is the same
        // as that when fetched by the canonical service name.
        self::assertSame($instance, $container->get('service'));

        // Now ensure that subsequent retrievals by alias retrieve the same
        // instance.
        self::assertSame($instance, $container->get('alias'));
    }

    /**
     * @dataProvider factoriesForDelegators
     */
    final public function testDelegatorsDoNotTriggerForAliasTargetingFactoryBasedServiceUsingAnyFactoryType(
        array $config
    ) : void {
        $config += [
            'aliases' => [
                'alias' => 'service',
            ],
            'delegators' => [
                'alias' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        $instance = $container->get('alias');
        self::assertInstanceOf(TestAsset\Service::class, $instance);
        self::assertNotInstanceOf(TestAsset\Delegator::class, $instance);

        // Now ensure that the instance already retrieved by alias is the same
        // as that when fetched by the canonical service name.
        self::assertSame($instance, $container->get('service'));
    }

    final public function invalidService()
    {
        yield 'non-existent-invokable' => [
            ['invokables' => [TestAsset\NonExistent::class]],
            TestAsset\NonExistent::class,
            TestAsset\NonExistent::class,
        ];

        yield 'non-existent-aliased-invokable' => [
            ['invokables' => ['service' => TestAsset\NonExistent::class]],
            'service',
            TestAsset\NonExistent::class,
        ];

        yield 'non-existent-factory' => [
            ['factories' => ['service' => TestAsset\NonExistent::class]],
            'service',
            'service',
        ];

        yield 'non-existent-aliased-factory' => [
            [
                'aliases' => ['alias' => 'service'],
                'factories' => ['service' => TestAsset\NonExistent::class],
            ],
            'alias',
            'service',
        ];

        yield 'invalid-invokable' => [
            ['invokables' => [TestAsset\FactoryWithRequiredParameters::class]],
            TestAsset\FactoryWithRequiredParameters::class,
            TestAsset\FactoryWithRequiredParameters::class,
        ];

        yield 'invalid-aliased-invokable' => [
            ['invokables' => ['service' => TestAsset\FactoryWithRequiredParameters::class]],
            'service',
            TestAsset\FactoryWithRequiredParameters::class,
        ];

        yield 'invalid-factory' => [
            ['factories' => ['service' => TestAsset\FactoryWithRequiredParameters::class]],
            'service',
            'service',
        ];

        yield 'invalid-aliased-factory' => [
            [
                'aliases' => ['alias' => 'service'],
                'factories' => ['service' => TestAsset\FactoryWithRequiredParameters::class],
            ],
            'alias',
            'service',
        ];

        yield 'non-invokable-factory' => [
            ['factories' => ['service' => TestAsset\NonInvokableFactory::class]],
            'service',
            'service',
        ];

        yield 'non-invokable-aliased-factory' => [
            [
                'aliases' => ['alias' => 'service'],
                'factories' => ['service' => TestAsset\NonInvokableFactory::class],
            ],
            'alias',
            'service',
        ];
    }

    /**
     * @dataProvider invalidService
     */
    final public function testWithDelegatorsResolvesToInvalidClassNoExceptionIsRaisedIfCallbackNeverInvoked(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ) : void {
        $container = $this->createContainer($config + [
            'delegators' => [
                $delegatedServiceName => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has($serviceNameToTest));
        $instance = $container->get($serviceNameToTest);
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
    }

    /**
     * @dataProvider invalidService
     */
    final public function testWithDelegatorsResolvesToInvalidClassAnExceptionIsRaisedWhenCallbackIsInvoked(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ) : void {
        $container = $this->createContainer($config + [
            'delegators' => [
                $delegatedServiceName => [
                    TestAsset\Delegator1Factory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has($serviceNameToTest));

        Assert::expectedExceptions(
            function () use ($container, $serviceNameToTest) {
                $container->get($serviceNameToTest);
            },
            [Error::class, ContainerExceptionInterface::class]
        );
    }
}
