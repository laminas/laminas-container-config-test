# laminas-container-config-test

[![Build Status](https://travis-ci.org/laminas/laminas-container-config-test.svg?branch=master)](https://travis-ci.org/laminas/laminas-container-config-test)
[![Coverage Status](https://coveralls.io/repos/github/laminas/laminas-container-config-test/badge.svg?branch=master)](https://coveralls.io/github/laminas/laminas-container-config-test?branch=master)

This library provides common tests for PSR-11 containers configured using a
subset of [laminas-servicemanager](https://github.com/laminas/laminas-servicemanager)
[configuration](https://docs.laminas.dev/laminas-servicemanager/configuring-the-service-manager/)
as [specified by Mezzio](https://docs.mezzio.dev/mezzio/v3/features/container/config/)

It guarantee delivery of the same basic functionality across multiple PSR-11
container implementations, and simplifies switching between them.

Currently we support:
- Aura.Di - via [laminas-auradi-config](https://github.com/laminas/laminas-auradi-config)
- Pimple - via [laminas-pimple-config](https://github.com/laminas/laminas-pimple-config)
- [laminas-servicemanager](https://github.com/laminas/laminas-servicemanager)

## Installation

Run the following to install this library:

```bash
$ composer require --dev laminas/laminas-container-config-test
```

## Using common tests

In your library, you will need to extend the
`Laminas\ContainerConfigTest\ContainerTest` class within your test suite and
implement the method `createContainer`:

```php
protected function createContainer(array $config) : ContainerInterface;
```

It should return your PSR-11-compatible container, configured using `$config`.

Then, depending on what functionality you'd like to support, you can add the
following traits into your test case:

- `Laminas\ContainerConfigTest\AliasTestTrait` - to support `aliases` configuration,
- `Laminas\ContainerConfigTest\DelegatorTestTrait` - to support `delegators` configuration,
- `Laminas\ContainerConfigTest\FactoryTestTrait` - to support `factories` configuration,
- `Laminas\ContainerConfigTest\InvokableTestTrait` - to support `invokables` configuration,
- `Laminas\ContainerConfigTest\ServiceTestTrait` - to support `services` configuration,

or use `Laminas\ContainerConfigTest\AllTestTrait` to support the entire configuration.
