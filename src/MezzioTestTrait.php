<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

trait MezzioTestTrait
{
    use AliasTestTrait;
    use DelegatorTestTrait;
    use FactoryTestTrait;
    use InvokableTestTrait;
    use ServiceTestTrait;
}
