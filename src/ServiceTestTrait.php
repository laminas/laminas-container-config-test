<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

trait ServiceTestTrait
{
    public function testService() : void
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
