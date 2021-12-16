<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

trait ServiceTestTrait
{
    final public function testFetchingServiceReturnsSameInstance(): void
    {
        $service = new TestAsset\Service();
        $config  = [
            'services' => [
                'foo-bar' => $service,
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        self::assertSame($service, $container->get('foo-bar'));
    }
}
