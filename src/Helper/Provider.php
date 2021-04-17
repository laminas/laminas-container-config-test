<?php

/**
 * @see       https://github.com/laminas/laminas-container-config-test for the canonical source repository
 * @copyright https://github.com/laminas/laminas-container-config-test/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\Helper;

use ArgumentCountError;
use Error;
use Generator;
use Laminas\ContainerConfigTest\TestAsset;

use function func_get_args;

/**
 * @internal
 */
class Provider
{
    /** @psalm-return Generator */
    public static function factory() : Generator
    {
        yield 'function-name' => [
            [
                'factories' => [
                    'service' => 'Laminas\ContainerConfigTest\TestAsset\function_factory',
                ],
            ],
        ];
        yield 'invokable-class-name' => [['factories' => ['service' => TestAsset\Factory::class]]];
        yield 'invokable-instance' => [['factories' => ['service' => new TestAsset\Factory()]]];
        yield 'callable-array' => [['factories' => ['service' => [TestAsset\FactoryStatic::class, 'create']]]];
        yield 'callable-string' => [['factories' => ['service' => TestAsset\FactoryStatic::class . '::create']]];
        yield 'closure' => [
            [
                'factories' => [
                    'service' => function () {
                        return new TestAsset\Service();
                    },
                ],
            ],
        ];
    }

    /** @psalm-return Generator */
    public static function factoryWithName() : Generator
    {
        yield 'function-name' => [
            [
                'factories' => [
                    'service' => 'Laminas\ContainerConfigTest\TestAsset\function_factory_with_name',
                ],
            ],
        ];
        yield 'invokable-class-name' => [['factories' => ['service' => TestAsset\FactoryWithName::class]]];
        yield 'invokable-instance' => [['factories' => ['service' => new TestAsset\FactoryWithName()]]];
        yield 'callable-array' => [['factories' => ['service' => [TestAsset\FactoryStatic::class, 'withName']]]];
        yield 'callable-string' => [['factories' => ['service' => TestAsset\FactoryStatic::class . '::withName']]];
        yield 'closure' => [
            [
                'factories' => [
                    'service' => function () {
                        return new TestAsset\FactoryService(func_get_args());
                    },
                ],
            ],
        ];
    }

    /**
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayAssignment
     * @psalm-return Generator
     */
    private static function aliased(callable $callable) : Generator
    {
        /**
         * @var string $name
         * @var mixed $params
         */
        foreach ($callable() as $name => $params) {
            /** @var mixed */
            $params[0]['aliases']['alias'] = $params[1];

            yield 'aliased-' . $name => [
                $params[0],
                'alias',
                $params[2],
                $params[3] ?? [],
            ];
        }
    }

    public static function invalidAliasedInvokable() : Generator
    {
        yield from self::aliased([__CLASS__, 'invalidInvokable']);
    }

    /**
     * @psalm-suppress UndefinedClass
     * @psalm-return Generator
     */
    public static function invalidInvokable() : Generator
    {
        yield 'non-existent-invokable' => [
            ['invokables' => [TestAsset\NonExistent::class]],
            TestAsset\NonExistent::class,
            TestAsset\NonExistent::class,
            [Error::class],
        ];

        yield 'non-existent-invokable-with-alias' => [
            ['invokables' => ['service' => TestAsset\NonExistent::class]],
            'service',
            TestAsset\NonExistent::class,
            [Error::class],
        ];

        yield 'invalid-invokable' => [
            ['invokables' => [TestAsset\FactoryWithRequiredParameters::class]],
            TestAsset\FactoryWithRequiredParameters::class,
            TestAsset\FactoryWithRequiredParameters::class,
            [ArgumentCountError::class],
        ];

        yield 'invalid-invokable-with-alias' => [
            ['invokables' => ['service' => TestAsset\FactoryWithRequiredParameters::class]],
            'service',
            TestAsset\FactoryWithRequiredParameters::class,
            [ArgumentCountError::class],
        ];
    }

    /** @psalm-return Generator */
    public static function invalidAliasedFactory() : Generator
    {
        yield from self::aliased([__CLASS__, 'invalidFactory']);
    }

    /** @psalm-return Generator */
    public static function invalidFactory() : Generator
    {
        /** @psalm-suppress UndefinedClass */
        yield 'non-existent-factory' => [
            ['factories' => ['service' => TestAsset\NonExistent::class]],
            'service',
            'service',
        ];

        yield 'invalid-factory' => [
            ['factories' => ['service' => TestAsset\FactoryWithRequiredParameters::class]],
            'service',
            'service',
            [ArgumentCountError::class],
        ];

        yield 'non-invokable-factory' => [
            ['factories' => ['service' => TestAsset\NonInvokableFactory::class]],
            'service',
            'service',
        ];

        yield 'non-class-factory' => [
            ['factories' => ['service' => 5]],
            'service',
            'service',
        ];

        yield 'array-non-static-factory' => [
            ['factories' => ['service' => [TestAsset\Factory::class, '__invoke']]],
            'service',
            'service',
        ];

        yield 'factory-as-string-to-service-factory' => [
            [
                'factories' => ['service' => 'factory'],
                'services' => ['factory' => new TestAsset\Factory()],
            ],
            'service',
            'service',
        ];
    }

    /** @psalm-return Generator */
    public static function invalidService() : Generator
    {
        yield from self::invalidInvokable();
        yield from self::invalidAliasedInvokable();
        yield from self::invalidFactory();
        yield from self::invalidAliasedFactory();
    }

    /** @psalm-return Generator */
    public static function aliasedAlias() : Generator
    {
        yield from self::aliased([__CLASS__, 'alias']);
    }

    /**
     * @psalm-suppress MixedOperand
     * @psalm-suppress MixedArrayAccess
     * @psalm-return Generator
     */
    public static function alias() : Generator
    {
        yield 'alias-service' => [
            [
                'aliases' => ['foo-bar' => 'service'],
                'services' => ['service' => new TestAsset\Service()],
            ],
            'foo-bar',
            'service',
        ];

        /**
         * @var string $name
         * @var mixed $params
         */
        foreach (self::invokable() as $name => $params) {
            yield 'alias-' . $name => [
                ['aliases' => ['foo-bar' => $params[1]]] + $params[0],
                'foo-bar',
                $params[2],
            ];
        }

        /**
         * @var string $name
         * @var mixed $params
         */
        foreach (self::factory() as $name => $params) {
            yield 'alias-factory-' . $name => [
                ['aliases' => ['foo-bar' => 'service']] + $params[0],
                'foo-bar',
                'service',
            ];
        }
    }

    /** @psalm-return Generator */
    public static function aliasedService() : Generator
    {
        yield from self::aliased([__CLASS__, 'service']);
    }

    /** @psalm-return Generator */
    public static function service() : Generator
    {
        yield from self::invokable();

        /**
         * @var string $name
         * @var mixed $params
         * @psalm-suppress MixedArrayAccess
         */
        foreach (self::factory() as $name => $params) {
            yield 'factory-service-' . $name => [
                $params[0],
                'service',
                'service',
            ];
        }
    }

    /** @psalm-return Generator */
    public static function invokable() : Generator
    {
        yield 'invokable' => [
            [
                'invokables' => [TestAsset\Service::class],
            ],
            TestAsset\Service::class,
            TestAsset\Service::class,
        ];

        yield 'invokable-with-key' => [
            [
                'invokables' => [TestAsset\Service::class => TestAsset\Service::class],
            ],
            TestAsset\Service::class,
            TestAsset\Service::class,
        ];

        yield 'invokable-with-alias' => [
            [
                'invokables' => ['service' => TestAsset\Service::class],
            ],
            'service',
            TestAsset\Service::class,
        ];
    }
}
