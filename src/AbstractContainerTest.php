<?php

declare(strict_types=1);

namespace Laminas\ContainerConfigTest;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class AbstractContainerTest extends TestCase
{
    abstract protected function createContainer(array $config) : ContainerInterface;
}
