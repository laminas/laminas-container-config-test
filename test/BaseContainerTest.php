<?php
/**
 * @see       https://github.com/zendframework/zend-container-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\ContainerTest;

use Zend\ContainerTest\ContainerTest;
use Zend\ContainerTest\FactoryTestTrait;
use Zend\ContainerTest\InvokableTestTrait;
use Zend\ContainerTest\SharedTestTrait;

abstract class BaseContainerTest extends ContainerTest
{
    use FactoryTestTrait;
    use InvokableTestTrait;
    use SharedTestTrait;
}
