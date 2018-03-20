# zend-container-config-test

[![Build Status](https://secure.travis-ci.org/zendframework/zend-container-config-test.svg?branch=master)](https://secure.travis-ci.org/zendframework/zend-container-config-test)
[![Coverage Status](https://coveralls.io/repos/github/zendframework/zend-container-config-test/badge.svg?branch=master)](https://coveralls.io/github/zendframework/zend-container-config-test?branch=master)

This library provides common tests for PSR-11 containers configured
[`zend-servicemanager`](https://github.com/zendframework/zend-servicemanager)
[configuration](https://docs.zendframework.com/zend-servicemanager/configuring-the-service-manager/).

It guarantee us to deliver the same functionality across multiple PSR-11
container implementations and simplify switching between them.

Currently we support:
- `Aura.Di` - via [`zend-auradi-config`](https://github.com/zendframework/zend-auradi-config)
- `Pimple` - via [`zend-pimple-config`](https://github.com/zendframework/zend-pimple-config)
- [`zend-servicemanager`](https://github.com/zendframework/zend-servicemanager)

## Installation

Run the following to install this library:

```bash
$ composer require --dev zendframework/zend-container-config-test
```

## Using common tests

In your library you have to extends `Zend\ContainerConfigTest\ContainerTest` class
and implement method `createContainer`:

```php
protected function createContainer(array $config) : ContainerInterface;
```

It should return configured PSR-11 container.

Then, depends what functionality you'd like to support, you can add the
following traits into your test case:

- `Zend\ContainerConfigTest\AliasTestTrait` - to support `aliases` configuration,
- `Zend\ContainerConfigTest\DelegatorTestTrait` - to support `delegators` configuration,
- `Zend\ContainerConfigTest\FactoryTestTrait` - to support `factories` configuration,
- `Zend\ContainerConfigTest\InvokableTestTrait` - to support `invokables` configuration,
- `Zend\ContainerConfigTest\ServiceTestTrait` - to support `services` configuration,

or use `Zend\ContainerConfigTest\AllTestTrait` to support whole `zend-servicemanager` configuration.
