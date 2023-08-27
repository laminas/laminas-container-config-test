<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use function assert;

/**
 * @psalm-require-extends AbstractContainerTest
 */
trait DelegatorTestTrait
{
    final public function testDelegatorsOperateOnInvokables(): void
    {
        assert($this instanceof AbstractContainerTest);

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

    final public function testDelegatorsDoNotOperateOnServices(): void
    {
        assert($this instanceof AbstractContainerTest);

        $myService = new TestAsset\Service();
        $config    = [
            'services'   => [
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

    final public function testDelegatorsApplyToInvokableServiceResolvedViaAlias(): void
    {
        assert($this instanceof AbstractContainerTest);

        $config = [
            'aliases'    => [
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

    final public function testDelegatorsNamedForAliasDoNotApplyToInvokableServiceResolvedViaAlias(): void
    {
        assert($this instanceof AbstractContainerTest);

        $config = [
            'aliases'    => [
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

    final public function testDelegatorsNamedForAliasDoNotApplyToInvokableServiceWithAlias(): void
    {
        assert($this instanceof AbstractContainerTest);

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

    final public function testDelegatorsDoNotApplyToAliasResolvingToServiceEntry(): void
    {
        assert($this instanceof AbstractContainerTest);

        $myService = new TestAsset\Service();
        $config    = [
            'aliases'    => [
                'alias' => 'foo-bar',
            ],
            'services'   => [
                'foo-bar' => $myService,
            ],
            'delegators' => [
                'alias'   => [
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

    final public function testDelegatorsDoNotTriggerForAliasTargetingInvokableService(): void
    {
        assert($this instanceof AbstractContainerTest);

        $config = [
            'aliases'    => [
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
     * @param array<string,mixed> $config
     */
    final public function testDelegatorsReceiveCallbackResolvingToReturnValueOfPrevious(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ): void {
        assert($this instanceof AbstractContainerTest);

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
     * @param array<string,mixed> $config
     */
    final public function testEmptyDelegatorListOriginalServiceShouldBeReturned(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ): void {
        assert($this instanceof AbstractContainerTest);

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

    final public function testMultipleAliasesForADelegatedInvokableServiceReceiveSameInstance(): void
    {
        assert($this instanceof AbstractContainerTest);

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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::factory
     * @param array<string,mixed> $config
     */
    final public function testDelegatorFactoriesTriggerForFactoryBackedServicesUsingAnyFactoryType(array $config): void
    {
        assert($this instanceof AbstractContainerTest);

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
     * @param array<string,mixed> $config
     */
    final public function testDelegatorsTriggerForFactoryServiceResolvedByAlias(array $config): void
    {
        assert($this instanceof AbstractContainerTest);

        $config += [
            'aliases'    => [
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
     * @param array<string,mixed> $config
     */
    final public function testDelegatorsDoNotTriggerForAliasTargetingFactoryBasedServiceUsingAnyFactoryType(
        array $config
    ): void {
        assert($this instanceof AbstractContainerTest);

        $config += [
            'aliases'    => [
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
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service()
     * @param array<string,mixed> $config
     */
    final public function testWithDelegatorsResolvesToInvalidClassNoExceptionIsRaisedIfCallbackNeverInvoked(
        array $config,
        string $serviceNameToTest,
        string $delegatedServiceName
    ): void {
        assert($this instanceof AbstractContainerTest);

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
}
