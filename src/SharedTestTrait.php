<?php
/**
 * @see       https://github.com/zendframework/zend-container-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerTest;

use Generator;

trait SharedTestTrait
{
    public function config() : Generator
    {
        yield 'factories' => [['factories' => ['service' => TestAsset\Factory::class]]];
        yield 'invokables' => [['invokables' => ['service' => TestAsset\Service::class]]];
        yield 'aliases-invokables' => [
            [
                'aliases' => ['service' => TestAsset\Service::class],
                'invokables' => [TestAsset\Service::class => TestAsset\Service::class],
            ],
        ];
        yield 'aliases-factories' => [
            [
                'aliases' => ['service' => TestAsset\Service::class],
                'factories' => [TestAsset\Service::class => TestAsset\Factory::class],
            ],
        ];
    }

    /**
     * @dataProvider config
     */
    public function testIsSharedByDefault(array $config) : void
    {
        $container = $this->createContainer($config);

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        self::assertSame($service1, $service2);
    }

    /**
     * @dataProvider config
     */
    public function testCanDisableSharedByDefault(array $config) : void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared_by_default' => false,
        ]));

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        self::assertNotSame($service1, $service2);
    }

    /**
     * @dataProvider config
     */
    public function testCanDisableSharedForSingleService(array $config) : void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared' => [
                'service' => false,
            ],
        ]));

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        self::assertNotSame($service1, $service2);
    }

    /**
     * @dataProvider config
     */
    public function testCanEnableSharedForSingleService(array $config) : void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared_by_default' => false,
            'shared' => [
                'service' => true,
            ],
        ]));

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        self::assertSame($service1, $service2);
    }

    public function testServiceIsSharedByDefault() : void
    {
        $service = new TestAsset\Service();
        $container = $this->createContainer([
            'services' => [
                'service' => $service,
            ],
        ]);

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        self::assertSame($service, $service1);
        self::assertSame($service, $service2);
    }

    public function testServiceIsSharedEvenIfSharedByDefaultIsFalse() : void
    {
        $service = new TestAsset\Service();
        $container = $this->createContainer([
            'services' => [
                'service' => $service,
            ],
            'shared_by_default' => false,
        ]);

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        self::assertSame($service, $service1);
        self::assertSame($service, $service2);
    }

    public function testServiceIsSharedEvenIfHasSharedSetToFalse() : void
    {
        $service = new TestAsset\Service();
        $container = $this->createContainer([
            'services' => [
                'service' => $service,
            ],
            'shared' => [
                'service' => false,
            ],
        ]);

        $service1 = $container->get('service');
        $service2 = $container->get('service');

        self::assertSame($service, $service1);
        self::assertSame($service, $service2);
    }

    public function testServiceIsSharedWhenAccessedByAlias() : void
    {
        $service = new TestAsset\Service();
        $container = $this->createContainer([
            'aliases' => [
                'alias' => 'service',
            ],
            'services' => [
                'service' => $service,
            ],
        ]);

        $aliasService = $container->get('alias');

        self::assertSame($service, $aliasService);
    }
}
