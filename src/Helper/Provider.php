<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest\Helper;

use Generator;
use Zend\ContainerConfigTest\TestAsset;

use function func_get_args;

/**
 * @internal
 */
class Provider
{
    public static function factory() : Generator
    {
        yield 'function-name' => [['factories' => ['service' => 'Zend\ContainerConfigTest\TestAsset\factory']]];
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

    public static function factoryWithName() : Generator
    {
        yield 'function-name' => [['factories' => ['service' => 'Zend\ContainerConfigTest\TestAsset\factoryWithName']]];
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

    private static function invalidAliased(callable $callable) : Generator
    {
        foreach ($callable() as $name => $params)
        {
            yield 'aliased-' . $name => [
                $params[0] + ['aliases' => ['alias' => $params[1]]],
                'alias',
                $params[2],
            ];
        }
    }

    public static function invalidAliasedInvokable() : Generator
    {
        yield from self::invalidAliased([__CLASS__, 'invalidInvokable']);
    }

    public static function invalidInvokable() : Generator
    {
        yield 'non-existent-invokable' => [
            ['invokables' => [TestAsset\NonExistent::class]],
            TestAsset\NonExistent::class,
            TestAsset\NonExistent::class,
        ];

        yield 'invalid-invokable' => [
            ['invokables' => [TestAsset\FactoryWithRequiredParameters::class]],
            TestAsset\FactoryWithRequiredParameters::class,
            TestAsset\FactoryWithRequiredParameters::class,
        ];

        yield 'non-invokable-invokable' => [
            ['invokables' => [TestAsset\NonInvokableFactory::class]],
            TestAsset\NonInvokableFactory::class,
            TestAsset\NonInvokableFactory::class,
        ];
    }

    public static function invalidAliasedFactory() : Generator
    {
        yield from self::invalidAliased([__CLASS__, 'invalidFactory']);
    }

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
    }

    public static function invalidService() : Generator
    {
        yield from self::invalidInvokable();
        yield from self::invalidAliasedInvokable();
        yield from self::invalidFactory();
        yield from self::invalidAliasedFactory();
    }
}
