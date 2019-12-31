<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

/**
 * Extend this class and implement createContainer in order to verify that
 * your container implementation will work as expected when provided with
 * Mezzio DI container configuration.
 */
abstract class AbstractMezzioContainerConfigTest extends AbstractContainerTest
{
    use AliasTestTrait;
    use DelegatorTestTrait;
    use FactoryTestTrait;
    use InvokableTestTrait;
    use ServiceTestTrait;
}
