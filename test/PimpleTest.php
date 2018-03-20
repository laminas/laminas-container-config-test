<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\ContainerConfigTest;

use Psr\Container\ContainerInterface;
use Zend\Pimple\Config\Config;
use Zend\Pimple\Config\ContainerFactory;

class PimpleTest extends BaseContainerTest
{
    public function createContainer(array $config) : ContainerInterface
    {
        $factory = new ContainerFactory();

        return $factory(new Config(['dependencies' => $config]));
    }
}
