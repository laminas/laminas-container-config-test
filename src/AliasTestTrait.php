<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use Generator;

trait AliasTestTrait
{
    final public function alias() : Generator
    {
        yield 'alias-service' => [
            [
                'aliases' => ['alias' => 'service'],
                'services' => ['service' => new TestAsset\Service()],
            ],
            'service',
        ];

        yield 'alias-invokable' => [
            [
                'aliases' => ['alias' => TestAsset\Service::class],
                'invokables' => [TestAsset\Service::class => TestAsset\Service::class],
            ],
            TestAsset\Service::class,
        ];

        yield 'alias-factory' => [
            [
                'aliases' => ['alias' => 'service'],
                'factories' => ['service' => TestAsset\Factory::class],
            ],
            'service',
        ];

        yield 'alias-alias-service' => [
            [
                'aliases' => [
                    'alias' => 'alias2',
                    'alias2' => 'service',
                ],
                'services' => [
                    'service' => new TestAsset\Service(),
                ],
            ],
            'service',
        ];

        yield 'alias-alias-invokable' => [
            [
                'aliases' => [
                    'alias' => 'alias2',
                    'alias2' => TestAsset\Service::class,
                ],
                'invokables' => [
                    TestAsset\Service::class => TestAsset\Service::class,
                ],
            ],
            TestAsset\Service::class,
        ];

        yield 'alias-alias-factory' => [
            [
                'aliases' => [
                    'alias' => 'alias2',
                    'alias2' => 'service',
                ],
                'factories' => [
                    'service' => TestAsset\Factory::class,
                ],
            ],
            'service',
        ];
    }

    /**
     * @dataProvider alias
     */
    final public function testRetrievingServiceByNameBeforeAliasOfServiceResultsInSameInstance(
        array $config,
        string $serviceToTest
    ) : void {
        $container = $this->createContainer($config);

        self::assertTrue($container->has($serviceToTest));
        self::assertTrue($container->has('alias'));
        self::assertSame($container->get($serviceToTest), $container->get('alias'));
    }

    /**
     * @dataProvider alias
     */
    final public function testRetrievingAliasedServiceBeforeResolvedServiceResultsInSameInstance(
        array $config,
        string $serviceToTest
    ) : void {
        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        self::assertTrue($container->has($serviceToTest));
        self::assertSame($container->get('alias'), $container->get($serviceToTest));
    }

    final public function testInstancesRetrievedByTwoAliasesResolvingToSameServiceMustBeTheSame() : void
    {
        $container = $this->createContainer([
            'aliases' => [
                'alias1' => TestAsset\Service::class,
                'alias2' => TestAsset\Service::class,
            ],
            'invokables' => [
                TestAsset\Service::class,
            ],
        ]);

        self::assertTrue($container->has('alias1'));
        self::assertTrue($container->has('alias2'));
        self::assertSame($container->get('alias1'), $container->get('alias2'));
    }
}
