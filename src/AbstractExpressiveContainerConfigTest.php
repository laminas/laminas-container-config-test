<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest;

/**
 * Extend this class and implement createContainer in order to verify that
 * your container implementation will work as expected when provided with
 * Expressive DI container configuration.
 */
abstract class AbstractExpressiveContainerConfigTest extends AbstractContainerTest
{
    use AliasTestTrait;
    use DelegatorTestTrait;
    use FactoryTestTrait;
    use InvokableTestTrait;
    use ServiceTestTrait;
}
