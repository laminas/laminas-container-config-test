<?php
/**
 * @see       https://github.com/zendframework/zend-container-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerTest;

use Generator;
use Psr\Container\NotFoundExceptionInterface;

trait FactoryTestTrait
{
    public function factory() : Generator
    {
        yield 'invokable' => [['factories' => ['service' => TestAsset\Factory::class]]];
        yield 'invokable-array' => [['factories' => ['service' => [TestAsset\FactoryStatic::class, 'create']]]];
        yield 'invokable-string' => [['factories' => ['service' => TestAsset\FactoryStatic::class . '::create']]];
        yield 'invokable-callback' => [
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
     * @dataProvider factory
     */
    public function testFactory(array $config) : void
    {
        $container = $this->createContainer($config);

        self::assertTrue($container->has('service'));
        self::assertInstanceOf(TestAsset\Service::class, $container->get('service'));
    }

    public function factoryWithName() : Generator
    {
        yield 'invokable' => [['factories' => ['service' => TestAsset\FactoryWithName::class]]];
        yield 'invokable-array' => [['factories' => ['service' => [TestAsset\FactoryStatic::class, 'withName']]]];
        yield 'invokable-string' => [['factories' => ['service' => TestAsset\FactoryStatic::class . '::withName']]];
        yield 'alias' => [
            [
                'factories' => ['service' => 'factory'],
                'services' => ['factory' => new TestAsset\FactoryWithName()],
            ]
        ];
    }

    /**
     * @dataProvider factoryWithName
     */
    public function testFactoryGetsServiceName(array $config) : void
    {
        $container = $this->createContainer($config);

        $args = $container->get('service');
        self::assertGreaterThanOrEqual(2, $args);
        self::assertSame($container, array_shift($args));
        self::assertEquals('service', array_shift($args));
    }

    public function testFactoryUsesAliasToService() : void
    {
        $container = $this->createContainer([
            'factories' => ['service' => 'factory'],
            'services' => ['factory' => new TestAsset\Factory()],
        ]);

        self::assertTrue($container->has('service'));

        $this->expectException(NotFoundExceptionInterface::class);
        $container->get('service');
    }
}
