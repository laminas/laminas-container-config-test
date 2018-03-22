<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest\Helper;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use Throwable;

use function get_class;
use function implode;
use function sprintf;

class Assert {

    public static function expectedExceptions(callable $function, array $exceptions) : void
    {
        $caught = false;

        try {
            $function();
        } catch (Throwable $e) {
            if (! self::isInstanceOf($e, $exceptions)) {
                PHPUnitAssert::fail(sprintf(
                    'Throwable of type %s (%s) was raised; expected one of %s',
                    get_class($e),
                    $e->getMessage(),
                    implode(', ', $exceptions)
                ));
            }

            $caught = true;
        }

        PHPUnitAssert::assertTrue(
            $caught,
            sprintf('No any of [%s] thrown when one was expected', implode(', ', $exceptions))
        );
    }

    private static function isInstanceOf(Throwable $e, array $types) : bool
    {
        foreach ($types as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }

        return false;
    }
}
