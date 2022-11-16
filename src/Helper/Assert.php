<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\Helper;

use PHPUnit\Framework\Assert as PHPUnitAssert;
use Throwable;

use function implode;
use function sprintf;

class Assert
{
    /** @param list<class-string<Throwable>> $exceptions */
    public static function expectedExceptions(callable $function, array $exceptions): void
    {
        $caught = false;

        try {
            $function();
        } catch (Throwable $e) {
            if (! self::isInstanceOf($e, $exceptions)) {
                PHPUnitAssert::fail(sprintf(
                    'Throwable of type %s (%s) was raised; expected one of %s',
                    $e::class,
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

    /** @param list<class-string<Throwable>> $types */
    private static function isInstanceOf(Throwable $e, array $types): bool
    {
        /** @var mixed $type */
        foreach ($types as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }

        return false;
    }
}
