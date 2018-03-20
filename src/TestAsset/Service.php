<?php
/**
 * @see       https://github.com/zendframework/zend-container-config-test for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-container-config-test/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\ContainerConfigTest\TestAsset;

class Service
{
    public $injected = [];

    /**
     * @param mixed $a
     * @return mixed
     */
    public function __invoke($a = null)
    {
        return $a;
    }

    public function inject(string $name) : void
    {
        $this->injected[] = $name;
    }
}
