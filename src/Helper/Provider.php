<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest\Helper;

use Generator;
use Laminas\ContainerConfigTest\TestAsset;

use function func_get_args;

/**
 * @internal
 */
class Provider
{
    /** @return Generator<non-empty-string,array{0:array<string,mixed>}> */
    public static function factory(): Generator
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

    /** @return Generator<non-empty-string,array{0:array<string,mixed>}> */
    public static function factoryWithName(): Generator
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
     * @param callable():iterable<non-empty-string,array{
     *     array<string,mixed>,
     *     non-empty-string,
     *     non-empty-string
     * }> $callable
     * @return Generator<non-empty-string,array{
     *     array<string,mixed>,
     *     non-empty-string,
     *     non-empty-string
     * }>
     */
    private static function aliased(callable $callable): Generator
    {
        foreach ($callable() as $name => [$config,, $serviceName]) {
            /** @var array<string,string> $aliases */
            $aliases           = $config['aliases'] ?? [];
            $aliases['alias']  = $serviceName;
            $config['aliases'] = $aliases;

            yield 'aliased-' . $name => [
                $config,
                'alias',
                $serviceName,
            ];
        }
    }

    /**
     * @return Generator<non-empty-string,array{
     *     array<string,mixed>,
     *     non-empty-string,
     *     non-empty-string
     * }>
     */
    public static function aliasedAlias(): Generator
    {
        yield from self::aliased(fn () => self::alias());
    }

    /**
     * @return Generator<non-empty-string,array{
     *     array<string,mixed>,
     *     non-empty-string,
     *     non-empty-string
     * }>
     */
    public static function alias(): Generator
    {
        yield 'alias-service' => [
            [
                'aliases'  => ['foo-bar' => 'service'],
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
     * @return Generator<non-empty-string,array{
     *     array<string,mixed>,
     *     non-empty-string,
     *     non-empty-string
     * }>
     */
    public static function aliasedService(): Generator
    {
        yield from self::aliased(fn() => self::service());
    }

    /**
     * @return Generator<non-empty-string,array{
     *     array<string,mixed>,
     *     non-empty-string,
     *     non-empty-string
     * }>
     */
    public static function service(): Generator
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
     * @return Generator<non-empty-string,array{
     *     array<string,mixed>,
     *     non-empty-string,
     *     class-string,
     * }>
     */
    public static function invokable(): Generator
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
