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
    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: array{factories: array{service: TestAsset\Factory|\Closure():TestAsset\Service|\Laminas\ContainerConfigTest\TestAsset\Factory::class|array{0: string, 1: string}|string}}}, mixed, void>
     */
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

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: array{factories: array{service: TestAsset\FactoryWithName|\Closure():TestAsset\FactoryService|\Laminas\ContainerConfigTest\TestAsset\FactoryWithName::class|array{0: string, 1: string}|string}}}, mixed, void>
     */
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
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: mixed, 1: string, 2: mixed, 3: array<empty, empty>|mixed}, mixed, void>
     */
    private static function aliased(callable $callable) : Generator
    {
        foreach ($callable() as $name => $params) {
            $params[0]['aliases']['alias'] = $params[1];

            yield 'aliased-' . $name => [
                $params[0],
                'alias',
                $params[2],
                $params[3] ?? [],
            ];
        }
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: mixed, 1: string, 2: mixed, 3: array<empty, empty>|mixed}, mixed, void>
     */
    public static function invalidAliasedInvokable() : Generator
    {
        yield from self::aliased([__CLASS__, 'invalidInvokable']);
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: array{invokables: array{service?: \Laminas\ContainerConfigTest\TestAsset\FactoryWithRequiredParameters::class|\Laminas\ContainerConfigTest\TestAsset\NonExistent::class, 0?: \Laminas\ContainerConfigTest\TestAsset\FactoryWithRequiredParameters::class|\Laminas\ContainerConfigTest\TestAsset\NonExistent::class}}, 1: \Laminas\ContainerConfigTest\TestAsset\FactoryWithRequiredParameters::class|\Laminas\ContainerConfigTest\TestAsset\NonExistent::class|string, 2: \Laminas\ContainerConfigTest\TestAsset\FactoryWithRequiredParameters::class|\Laminas\ContainerConfigTest\TestAsset\NonExistent::class, 3: array{0: ArgumentCountError::class|Error::class}}, mixed, void>
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

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: mixed, 1: string, 2: mixed, 3: array<empty, empty>|mixed}, mixed, void>
     */
    public static function invalidAliasedFactory() : Generator
    {
        yield from self::aliased([__CLASS__, 'invalidFactory']);
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: array{factories: array{service: \Laminas\ContainerConfigTest\TestAsset\FactoryWithRequiredParameters::class|\Laminas\ContainerConfigTest\TestAsset\NonExistent::class|\Laminas\ContainerConfigTest\TestAsset\NonInvokableFactory::class|array{0: string, 1: string}|int|string}, services?: array{factory: TestAsset\Factory}}, 1: string, 2: string, 3?: array{0: string}}, mixed, void>
     */
    public static function invalidFactory() : Generator
    {
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

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: array{invokables?: array{service?: \Laminas\ContainerConfigTest\TestAsset\FactoryWithRequiredParameters::class|\Laminas\ContainerConfigTest\TestAsset\NonExistent::class, 0?: \Laminas\ContainerConfigTest\TestAsset\FactoryWithRequiredParameters::class|\Laminas\ContainerConfigTest\TestAsset\NonExistent::class}, factories?: array{service: array{0: string, 1: string}|int|string}, services?: array{factory: TestAsset\Factory}}|mixed, 1: string, 2: mixed|string, 3?: array{0?: string}|mixed}, mixed, void>
     */
    public static function invalidService() : Generator
    {
        yield from self::invalidInvokable();
        yield from self::invalidAliasedInvokable();
        yield from self::invalidFactory();
        yield from self::invalidAliasedFactory();
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: mixed, 1: string, 2: mixed, 3: array<empty, empty>|mixed}, mixed, void>
     */
    public static function aliasedAlias() : Generator
    {
        yield from self::aliased([__CLASS__, 'alias']);
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: array{aliases: array{foo-bar: string}, factories?: array{service: TestAsset\Factory|\Closure():TestAsset\Service|array{0: string, 1: string}|string}, services?: array{service: TestAsset\Service}, invokables?: array{service?: string, 0?: string, LaminasContainerConfigTestTestAssetService?: string}}, 1: string, 2: string}, mixed, void>
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

        foreach (self::invokable() as $name => $params) {
            yield 'alias-' . $name => [
                ['aliases' => ['foo-bar' => $params[1]]] + $params[0],
                'foo-bar',
                $params[2],
            ];
        }

        foreach (self::factory() as $name => $params) {
            yield 'alias-factory-' . $name => [
                ['aliases' => ['foo-bar' => 'service']] + $params[0],
                'foo-bar',
                'service',
            ];
        }
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: mixed, 1: string, 2: mixed, 3: array<empty, empty>|mixed}, mixed, void>
     */
    public static function aliasedService() : Generator
    {
        yield from self::aliased([__CLASS__, 'service']);
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: array{factories?: array{service: TestAsset\Factory|\Closure():TestAsset\Service|array{0: string, 1: string}|string}, invokables?: array{service?: string, 0?: string, LaminasContainerConfigTestTestAssetService?: string}}, 1: string, 2: string}, mixed, void>
     */
    public static function service() : Generator
    {
        yield from self::invokable();

        foreach (self::factory() as $name => $params) {
            yield 'factory-service-' . $name => [
                $params[0],
                'service',
                'service',
            ];
        }
    }

    /**
     * @return Generator
     *
     * @psalm-return Generator<string, array{0: array{invokables: array{service?: string, 0?: string, 'Laminas\\ContainerConfigTest\\TestAsset\\Service'?: string}}, 1: \Laminas\ContainerConfigTest\TestAsset\Service::class|string, 2: string}, mixed, void>
     */
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
