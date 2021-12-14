<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use function array_merge;

trait SharedTestTrait
{
    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
     * @param array<string,mixed> $config
     */
    final public function testIsSharedByDefault(array $config, string $serviceToTest): void
    {
        $container = $this->createContainer($config);

        /** @var mixed $service1 */
        $service1 = $container->get($serviceToTest);
        /** @var mixed $service2 */
        $service2 = $container->get($serviceToTest);

        self::assertSame($service1, $service2);
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
     * @param array<string,mixed> $config
     */
    final public function testCanDisableSharedByDefault(array $config, string $serviceToTest): void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared_by_default' => false,
        ]));

        /** @var mixed $service1 */
        $service1 = $container->get($serviceToTest);
        /** @var mixed $service2 */
        $service2 = $container->get($serviceToTest);

        self::assertNotSame($service1, $service2);
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
     * @param array<string,mixed> $config
     */
    final public function testCanDisableSharedForSingleService(array $config, string $serviceToTest): void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared' => [
                $serviceToTest => false,
            ],
        ]));

        /** @var mixed $service1 */
        $service1 = $container->get($serviceToTest);
        /** @var mixed $service2 */
        $service2 = $container->get($serviceToTest);

        self::assertNotSame($service1, $service2);
    }

    /**
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::service
     * @dataProvider \Laminas\ContainerConfigTest\Helper\Provider::aliasedService
     * @param array<string,mixed> $config
     */
    final public function testCanEnableSharedForSingleService(array $config, string $serviceToTest): void
    {
        $container = $this->createContainer(array_merge($config, [
            'shared_by_default' => false,
            'shared'            => [
                $serviceToTest => true,
            ],
        ]));

        /** @var mixed $service1 */
        $service1 = $container->get($serviceToTest);
        /** @var mixed $service2 */
        $service2 = $container->get($serviceToTest);

        self::assertSame($service1, $service2);
    }

    final public function testServiceIsSharedByDefault(): void
    {
        $service   = new TestAsset\Service();
        $container = $this->createContainer([
            'services' => [
                'service' => $service,
            ],
        ]);

        /** @var mixed $service1 */
        $service1 = $container->get('service');
        /** @var mixed $service2 */
        $service2 = $container->get('service');

        self::assertSame($service, $service1);
        self::assertSame($service, $service2);
    }

    final public function testServiceIsSharedEvenIfSharedByDefaultIsFalse(): void
    {
        $service   = new TestAsset\Service();
        $container = $this->createContainer([
            'services'          => [
                'service' => $service,
            ],
            'shared_by_default' => false,
        ]);

        /** @var mixed $service1 */
        $service1 = $container->get('service');
        /** @var mixed $service2 */
        $service2 = $container->get('service');

        self::assertSame($service, $service1);
        self::assertSame($service, $service2);
    }

    final public function testServiceIsSharedEvenIfHasSharedSetToFalse(): void
    {
        $service   = new TestAsset\Service();
        $container = $this->createContainer([
            'services' => [
                'service' => $service,
            ],
            'shared'   => [
                'service' => false,
            ],
        ]);

        /** @var mixed $service1 */
        $service1 = $container->get('service');
        /** @var mixed $service2 */
        $service2 = $container->get('service');

        self::assertSame($service, $service1);
        self::assertSame($service, $service2);
    }

    final public function testServiceIsSharedWhenAccessedByAlias(): void
    {
        $service   = new TestAsset\Service();
        $container = $this->createContainer([
            'aliases'  => [
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
