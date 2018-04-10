<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\ContainerConfigTest;

use Psr\Container\ContainerInterface;
use Zend\ContainerConfigTest\SharedTestTrait;
use Zend\ServiceManager\ServiceManager;

class ServiceManagerTest extends BaseContainerTest
{
    use SharedTestTrait;

    public function createContainer(array $config) : ContainerInterface
    {
        return new ServiceManager($config);
    }
}
