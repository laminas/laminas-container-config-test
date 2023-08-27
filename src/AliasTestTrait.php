<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use Laminas\ContainerConfigTest\Helper\Provider;
use PHPUnit\Framework\Attributes\DataProviderExternal;

use function assert;

/**
 * @psalm-require-extends AbstractContainerTest
 */
trait AliasTestTrait
{
    /**
     * @param array<string,mixed> $config
     */
    #[DataProviderExternal(Provider::class, 'alias')]
    #[DataProviderExternal(Provider::class, 'aliasedAlias')]
    final public function testRetrievingServiceByNameBeforeAliasOfServiceResultsInSameInstance(
        array $config,
        string $alias,
        string $name
    ): void {
        assert($this instanceof AbstractContainerTest);

        $container = $this->createContainer($config);

        self::assertTrue($container->has($name));
        self::assertTrue($container->has($alias));
        self::assertSame($container->get($name), $container->get($alias));
    }

    /**
     * @param array<string,mixed> $config
     */
    #[DataProviderExternal(Provider::class, 'alias')]
    #[DataProviderExternal(Provider::class, 'aliasedAlias')]
    final public function testRetrievingAliasedServiceBeforeResolvedServiceResultsInSameInstance(
        array $config,
        string $alias,
        string $name
    ): void {
        assert($this instanceof AbstractContainerTest);

        $container = $this->createContainer($config);

        self::assertTrue($container->has($alias));
        self::assertTrue($container->has($name));
        self::assertSame($container->get($alias), $container->get($name));
    }

    final public function testInstancesRetrievedByTwoAliasesResolvingToSameServiceMustBeTheSame(): void
    {
        $container = $this->createContainer([
            'aliases'    => [
                'alias1' => TestAsset\Service::class,
                'alias2' => TestAsset\Service::class,
            ],
            'invokables' => [
                TestAsset\Service::class,
            ],
        ]);

        self::assertTrue($container->has('alias1'));
        self::assertTrue($container->has('alias2'));
        self::assertSame($container->get('alias1'), $container->get('alias2'));
    }
}
