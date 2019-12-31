<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\ContainerConfigTest;

use Laminas\ContainerConfigTest\AbstractMezzioContainerConfigTest;
use Laminas\Pimple\Config\Config;
use Laminas\Pimple\Config\ContainerFactory;
use Psr\Container\ContainerInterface;

class PimpleTest extends AbstractMezzioContainerConfigTest
{
    public function createContainer(array $config) : ContainerInterface
    {
        $factory = new ContainerFactory();

        return $factory(new Config(['dependencies' => $config]));
    }
}
