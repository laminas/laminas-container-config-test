<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

trait ServiceTestTrait
{
    final public function testFetchingServiceReturnsSameInstance() : void
    {
        $service = new TestAsset\Service();
        $config = [
            'services' => [
                'foo-bar' => $service,
            ],
        ];

        $container = $this->createContainer($config);

        self::assertTrue($container->has('foo-bar'));
        self::assertSame($service, $container->get('foo-bar'));
    }
}
