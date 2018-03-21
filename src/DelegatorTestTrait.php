<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

use Error;
use Generator;
use Psr\Container\ContainerExceptionInterface;
use Throwable;
use TypeError;

trait DelegatorTestTrait
{
    public function testDelegatorsOperateOnInvokables() : void
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

    public function testDelegatorsDoNotOperateOnServices() : void
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

    public function testDelegatorsApplyToInvokableServiceResolvedViaAlias() : void
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

    public function testDelegatorsNamedForAliasDoNotApplyToInvokableServiceResolvedViaAlias() : void
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

    public function testDelegatorsNamedForAliasDoNotApplyToInvokableServiceWithAlias() : void
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

    public function testDelegatorsDoNotApplyToAliasResolvingToServiceEntry() : void
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

    public function testDelegatorsDoNotTriggerForAliasTargetingInvokableService() : void
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

    public function delegatorService() : Generator
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
    public function testDelegatorsReceiveCallbackResolvingToReturnValueOfPrevious(
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

    public function testMultipleAliasesForADelegatedInvokableServiceReceiveSameInstance()
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

    public function testNonInvokableDelegatorClassNameResultsInExceptionDuringInstanceRetrieval()
    {
        $container = $this->createContainer([
            'invokables' => [
                TestAsset\Service::class => TestAsset\Service::class,
            ],
            'delegators' => [
                TestAsset\Service::class => [
                    TestAsset\NonInvokableFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has(TestAsset\Service::class));
        $this->expectException(ContainerExceptionInterface::class);
        $container->get(TestAsset\Service::class);
    }

    public function testNonExistentDelegatorClassResultsInExceptionDuringInstanceRetrieval()
    {
        $container = $this->createContainer([
            'invokables' => [
                TestAsset\Service::class => TestAsset\Service::class,
            ],
            'delegators' => [
                TestAsset\Service::class => [
                    TestAsset\NonExistentDelegatorFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has(TestAsset\Service::class));
        $this->expectException(ContainerExceptionInterface::class);
        $container->get(TestAsset\Service::class);
    }

    public function testDelegatorClassNameRequiringConstructorArgumentsResultsInExceptionDuringInstanceRetrieval()
    {
        $container = $this->createContainer([
            'invokables' => [
                TestAsset\Service::class => TestAsset\Service::class,
            ],
            'delegators' => [
                TestAsset\Service::class => [
                    TestAsset\FactoryWithRequiredParameters::class,
                ],
            ],
        ]);

        self::assertTrue($container->has(TestAsset\Service::class));

        $caught = false;
        try {
            $container->get(TestAsset\Service::class);
        } catch (Throwable $e) {
            if ($e instanceof TypeError || $e instanceof ContainerExceptionInterface) {
                $caught = true;
            }
        }

        $this->assertTrue($caught, 'No TypeError or ContainerExceptionInterface thrown when one was expected');
    }

    public function factoriesForDelegators() : Generator
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
    public function testDelegatorFactoriesTriggerForFactoryBackedServicesUsingAnyFactoryType(array $config)
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
    public function testDelegatorsTriggerForFactoryServiceResolvedByAlias(array $config) : void
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
    public function testDelegatorsDoNotTriggerForAliasTargetingFactoryBasedServiceUsingAnyFactoryType(
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
    
    // @codingStandardsIgnoreStart
    public function testWhenInvokableWithDelegatorsResolvesToNonExistentClassNoExceptionIsRaisedIfCallbackNeverInvoked()
    {
        // @codingStandardsIgnoreEnd
        $container = $this->createContainer([
            'invokables' => [
                TestAsset\NonExistent::class,
            ],
            'delegators' => [
                TestAsset\NonExistent::class => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has(TestAsset\NonExistent::class));
        $instance = $container->get(TestAsset\NonExistent::class);
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
    }
    
    // @codingStandardsIgnoreStart
    public function testWhenInvokableWithDelegatorsResolvesToInvalidClassAnExceptionIsRaisedIfCallbackNeverInvoked()
    {
        // @codingStandardsIgnoreEnd
        $container = $this->createContainer([
            'invokables' => [
                TestAsset\FactoryWithRequiredParameters::class,
            ],
            'delegators' => [
                TestAsset\FactoryWithRequiredParameters::class => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has(TestAsset\FactoryWithRequiredParameters::class));
        $instance = $container->get(TestAsset\FactoryWithRequiredParameters::class);
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
    }
    
    public function testWhenInvokableWithDelegatorsResolvesToNonExistentClassAnExceptionIsRaisedWhenCallbackIsInvoked()
    {
        $container = $this->createContainer([
            'invokables' => [
                TestAsset\NonExistent::class,
            ],
            'delegators' => [
                TestAsset\NonExistent::class => [
                    TestAsset\Delegator1Factory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has(TestAsset\NonExistent::class));

        $caught = false;
        try {
            $container->get(TestAsset\NonExistent::class);
        } catch (Throwable $e) {
            if (! $e instanceof Error && ! $e instanceof TypeError && ! $e instanceof ContainerExceptionInterface) {
                $this->fail(sprintf(
                    'Throwable of type %s (%s) was raised; expected Error, TypeError, or %s',
                    get_class($e),
                    $e->getMessage(),
                    ContainerExceptionInterface::class
                ));
            }
            $caught = true;
        }

        $this->assertTrue($caught, 'No TypeError or ContainerExceptionInterface thrown when one was expected');
    }
    
    // @codingStandardsIgnoreStart
    public function testWhenInvokableWithDelegatorsResolvesToInvalidFactoryClassAnExceptionIsRaisedWhenCallbackIsInvoked()
    {
        // @codingStandardsIgnoreEnd
        $container = $this->createContainer([
            'invokables' => [
                TestAsset\FactoryWithRequiredParameters::class,
            ],
            'delegators' => [
                'service' => [
                    TestAsset\Delegator1Factory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has(TestAsset\FactoryWithRequiredParameters::class));

        $caught = false;
        try {
            $container->get(TestAsset\FactoryWithRequiredParameters::class);
        } catch (Throwable $e) {
            if (! $e instanceof TypeError && ! $e instanceof ContainerExceptionInterface) {
                $this->fail(sprintf(
                    'Throwable of type %s (%s) was raised; expected TypeError or %s',
                    get_class($e),
                    $e->getMessage(),
                    ContainerExceptionInterface::class
                ));
            }
            $caught = true;
        }

        $this->assertTrue($caught, 'No TypeError or ContainerExceptionInterface thrown when one was expected');
    }
    
    // @codingStandardsIgnoreStart
    public function testWhenServiceWithDelegatorsResolvesToNonExistentFactoryClassNoExceptionIsRaisedIfCallbackNeverInvoked()
    {
        // @codingStandardsIgnoreEnd
        $container = $this->createContainer([
            'factories' => [
                'service' => TestAsset\NonExistentFactory::class,
            ],
            'delegators' => [
                'service' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has('service'));
        $instance = $container->get('service');
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
    }
    
    // @codingStandardsIgnoreStart
    public function testWhenServiceWithDelegatorsResolvesToInvalidFactoryClassAnExceptionIsRaisedIfCallbackNeverInvoked()
    {
        // @codingStandardsIgnoreEnd
        $container = $this->createContainer([
            'factories' => [
                'service' => TestAsset\FactoryWithRequiredParameters::class,
            ],
            'delegators' => [
                'service' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has('service'));
        $instance = $container->get('service');
        self::assertInstanceOf(TestAsset\Delegator::class, $instance);
    }
    
    // @codingStandardsIgnoreStart
    public function testWhenServiceWithDelegatorsResolvesToNonExistentFactoryClassAnExceptionIsRaisedWhenCallbackIsInvoked()
    {
        // @codingStandardsIgnoreEnd
        $container = $this->createContainer([
            'factories' => [
                'service' => TestAsset\NonExistentFactory::class,
            ],
            'delegators' => [
                'service' => [
                    TestAsset\Delegator1Factory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has('service'));
        $this->expectException(ContainerExceptionInterface::class);
        $container->get('service');
    }
    
    public function testWhenServiceWithDelegatorsResolvesToInvalidFactoryClassAnExceptionIsRaisedWhenCallbackIsInvoked()
    {
        $container = $this->createContainer([
            'factories' => [
                'service' => TestAsset\FactoryWithRequiredParameters::class,
            ],
            'delegators' => [
                'service' => [
                    TestAsset\Delegator1Factory::class,
                ],
            ],
        ]);

        self::assertTrue($container->has('service'));

        $caught = false;
        try {
            $container->get('service');
        } catch (Throwable $e) {
            if (! $e instanceof TypeError && ! $e instanceof ContainerExceptionInterface) {
                $this->fail(sprintf(
                    'Throwable of type %s (%s) was raised; expected TypeError or %s',
                    get_class($e),
                    $e->getMessage(),
                    ContainerExceptionInterface::class
                ));
            }
            $caught = true;
        }

        $this->assertTrue($caught, 'No TypeError or ContainerExceptionInterface thrown when one was expected');
    }
}
