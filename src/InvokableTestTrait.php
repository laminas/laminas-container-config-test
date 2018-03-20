<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

trait InvokableTestTrait
{
    public function testInvokableWithoutAlias() : void
    {
        $config = [
            'invokables' => [
                TestAsset\Service::class,
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has(TestAsset\Service::class));
        $service = $container->get(TestAsset\Service::class);
        self::assertInstanceOf(TestAsset\Service::class, $service);
        self::assertTrue($container->has('0'));
    }

    public function testInvokableWithAlias() : void
    {
        $config = [
            'invokables' => [
                'alias' => TestAsset\Service::class,
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('alias'));
        $service = $container->get('alias');
        self::assertInstanceOf(TestAsset\Service::class, $service);
        self::assertTrue($container->has(TestAsset\Service::class));
        $originService = $container->get(TestAsset\Service::class);
        self::assertInstanceOf(TestAsset\Service::class, $originService);
        self::assertSame($service, $originService);
        self::assertSame($originService, $container->get(TestAsset\Service::class));
    }
}
