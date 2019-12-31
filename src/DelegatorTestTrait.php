<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use ArgumentCountError;
use Error;
use Laminas\ContainerConfigTest\Helper\Assert;
use Psr\Container\ContainerExceptionInterface;

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

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
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

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::factory
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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::factory
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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::factory
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

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::invalidService
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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::invalidService
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
