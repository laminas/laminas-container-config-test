<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use function array_merge;

trait SharedTestTrait
{
    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
     */
    final public function testIsSharedByDefault(array $config, string $serviceToTest) : void
    {
        $container = $this->createContainer($config);

        $service1 = $container->get($serviceToTest);
        $service2 = $container->get($serviceToTest);

        self::assertSame($service1, $service2);
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
     */
    final public function testCanDisableSharedByDefault(array $config, string $serviceToTest) : void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared_by_default' => false,
        ]));

        $service1 = $container->get($serviceToTest);
        $service2 = $container->get($serviceToTest);

        self::assertNotSame($service1, $service2);
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
     */
    final public function testCanDisableSharedForSingleService(array $config, string $serviceToTest) : void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared' => [
                $serviceToTest => false,
            ],
        ]));

        $service1 = $container->get($serviceToTest);
        $service2 = $container->get($serviceToTest);

        self::assertNotSame($service1, $service2);
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
     */
    final public function testCanEnableSharedForSingleService(array $config, string $serviceToTest) : void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared_by_default' => false,
            'shared' => [
                $serviceToTest => true,
            ],
        ]));

        $service1 = $container->get($serviceToTest);
        $service2 = $container->get($serviceToTest);

        self::assertSame($service1, $service2);
    }

    final public function testServiceIsSharedByDefault() : void
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

    final public function testServiceIsSharedEvenIfSharedByDefaultIsFalse() : void
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

    final public function testServiceIsSharedEvenIfHasSharedSetToFalse() : void
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

    final public function testServiceIsSharedWhenAccessedByAlias() : void
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
