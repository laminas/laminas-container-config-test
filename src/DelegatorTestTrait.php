<?php
/**
 * @see       https://github.com/zendframework/zend-container-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerTest;

use Generator;

trait DelegatorTestTrait
{
    public function testDelegatorForInvokable() : void
    {
        $config = [
            'invokables' => [
                'foo-bar' => TestAsset\Service::class,
            ],
            'delegators' => [
                'foo-bar' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        $delegator = $container->get('foo-bar');
        self::assertInstanceOf(TestAsset\Delegator::class, $delegator);
        self::assertInstanceOf(TestAsset\Service::class, ($delegator->callback)());
        self::assertSame($delegator, $container->get('foo-bar'));
    }

    public function testDelegatorForService() : void
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
        $delegator = $container->get('foo-bar');
        self::assertInstanceOf(TestAsset\Delegator::class, $delegator);
        self::assertSame($myService, ($delegator->callback)());
        self::assertSame($delegator, $container->get('foo-bar'));
    }

    public function testDelegatorForFactory() : void
    {
        $config = [
            'factories' => [
                'foo-bar' => TestAsset\Factory::class,
            ],
            'delegators' => [
                'foo-bar' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        $delegator = $container->get('foo-bar');
        self::assertInstanceOf(TestAsset\Delegator::class, $delegator);
        self::assertInstanceOf(TestAsset\Service::class, ($delegator->callback)());
        self::assertSame($delegator, $container->get('foo-bar'));
    }

    public function testDelegatorForAliasInvokable() : void
    {
        $config = [
            'aliases' => [
                'alias' => 'foo-bar',
            ],
            'invokables' => [
                'foo-bar' => TestAsset\Service::class,
            ],
            'delegators' => [
                'alias' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        $delegator = $container->get('foo-bar');
        self::assertInstanceOf(TestAsset\Delegator::class, $delegator);
        self::assertInstanceOf(TestAsset\Service::class, ($delegator->callback)());
        self::assertSame($delegator, $container->get('foo-bar'));
    }

    public function testDelegatorForAliasService() : void
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
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        $delegator = $container->get('foo-bar');
        self::assertInstanceOf(TestAsset\Delegator::class, $delegator);
        self::assertSame($myService, ($delegator->callback)());
        self::assertSame($delegator, $container->get('foo-bar'));
    }

    public function testDelegatorForAliasFactory() : void
    {
        $config = [
            'aliases' => [
                'alias' => 'foo-bar',
            ],
            'factories' => [
                'foo-bar' => TestAsset\Factory::class,
            ],
            'delegators' => [
                'alias' => [
                    TestAsset\DelegatorFactory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        $delegator = $container->get('foo-bar');
        self::assertInstanceOf(TestAsset\Delegator::class, $delegator);
        self::assertInstanceOf(TestAsset\Service::class, ($delegator->callback)());
        self::assertSame($delegator, $container->get('foo-bar'));
    }

    public function delegatorService() : Generator
    {
        yield 'invokable' => [
            [
                'invokables' => ['foo-bar' => TestAsset\Service::class],
            ],
        ];

        yield 'service' => [
            [
                'services' => ['foo-bar' => new TestAsset\Service()],
            ],
        ];

        yield 'factory' => [
            [
                'factories' => ['foo-bar' => TestAsset\Factory::class],
            ],
        ];

        yield 'alias-invokable' => [
            [
                'aliases' => ['foo-bar' => 'alias'],
                'invokables' => ['alias' => TestAsset\Service::class],
            ],
        ];

        yield 'alias-service' => [
            [
                'aliases' => ['foo-bar' => 'alias'],
                'services' => ['alias' => new TestAsset\Service()],
            ],
        ];

        yield 'alias-factory' => [
            [
                'aliases' => ['foo-bar' => 'alias'],
                'factories' => ['alias' => TestAsset\Factory::class],
            ],
        ];
    }

    /**
     * @dataProvider delegatorService
     */
    public function testDelegatorMultipleDelegators(array $config) : void
    {
        $config += [
            'delegators' => [
                'foo-bar' => [
                    TestAsset\Delegator1Factory::class,
                    TestAsset\Delegator2Factory::class,
                ],
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        $service = $container->get('foo-bar');
        self::assertInstanceOf(TestAsset\Service::class, $service);
        self::assertEquals(
            [
                TestAsset\Delegator1Factory::class,
                TestAsset\Delegator2Factory::class,
            ],
            $service->injected
        );
        self::assertSame($service, $container->get('foo-bar'));
    }
}
