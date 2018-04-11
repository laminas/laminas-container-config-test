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
}
