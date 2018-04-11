<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

use Generator;
use Zend\ContainerConfigTest\Helper\Provider;

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

        foreach (Provider::factory() as $name => $params) {
            yield 'alias-factory-' . $name => [
                [
                    'aliases' => [
                        'alias' => 'service',
                    ],
                ] + $params[0],
                'service',
            ];

            yield 'alias-alias-factory-' . $name => [
                [
                    'aliases' => [
                        'alias' => 'alias2',
                        'alias2' => 'service',
                    ],
                ] + $params[0],
                'service',
            ];
        }
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
