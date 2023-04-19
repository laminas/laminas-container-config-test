<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use function assert;

/**
 * @psalm-require-extends AbstractContainerTest
 */
trait ServiceTestTrait
{
    final public function testFetchingServiceReturnsSameInstance(): void
    {
        assert($this instanceof AbstractContainerTest);
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
