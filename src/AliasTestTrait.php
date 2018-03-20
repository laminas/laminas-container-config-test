<?php
/**
 * @see       https://github.com/zendframework/zend-container-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerTest;

use Generator;

trait AliasTestTrait
{
    public function alias() : Generator
    {
        yield 'alias-service' => [
            [
                'aliases' => ['alias' => 'service'],
                'services' => ['service' => new TestAsset\Service()],
            ],
        ];

        yield 'alias-invokable' => [
            [
                'aliases' => ['alias' => 'service'],
                'invokables' => ['service' => TestAsset\Service::class],
            ],
        ];

        yield 'alias-factory' => [
            [
                'aliases' => ['alias' => 'service'],
                'factories' => ['service' => TestAsset\Factory::class],
            ],
        ];

        yield 'alias-delegator' => [
            [
                'aliases' => ['alias' => 'service'],
                'factories' => [
                    'service' => TestAsset\Factory::class,
                ],
                'delegators' => [
                    'service' => [
                        TestAsset\DelegatorFactory::class,
                    ],
                ],
            ],
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
        ];

        yield 'alias-alias-invokable' => [
            [
                'aliases' => [
                    'alias' => 'alias2',
                    'alias2' => 'service',
                ],
                'invokables' => [
                    'service' => TestAsset\Service::class,
                ],
            ],
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
        ];

        yield 'alias-alias-delegator' => [
            [
                'aliases' => [
                    'alias' => 'alias2',
                    'alias2' => 'service',
                ],
                'factories' => [
                    'service' => TestAsset\Factory::class,
                ],
                'delegators' => [
                    'service' => [
                        TestAsset\DelegatorFactory::class,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider alias
     */
    public function testAliasGetServiceFirst(array $config) : void
    {
        $container = $this->createContainer($config);

        self::assertTrue($container->has('service'));
        self::assertTrue($container->has('alias'));
        self::assertSame($container->get('service'), $container->get('alias'));
    }

    /**
     * @dataProvider alias
     */
    public function testAliasGetAliasFirst(array $config) : void
    {
        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        self::assertTrue($container->has('service'));
        self::assertSame($container->get('alias'), $container->get('service'));
    }
}
