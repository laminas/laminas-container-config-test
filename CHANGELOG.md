# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.2.2 - TBD

### Added

- [#11](https://github.com/zendframework/zend-container-config-test/pull/11) adds support for PHP 7.3.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.1 - 2018-04-12

### Added

- Nothing.

### Changed

- [#9](https://github.com/zendframework/zend-container-config-test/pull/9) changes several test asset function names
  in order to prevent collisions, as well as ensure all lines are covered during
  testing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.0 - 2018-04-11

### Added

- [#7](https://github.com/zendframework/zend-container-config-test/pull/7) adds
  the class `Zend\ContainerConfigTest\Helper\Provider`, containing data
  providers consumed by the various test traits.

### Changed

- [#7](https://github.com/zendframework/zend-container-config-test/pull/7) marks
  all methods in `Zend\ContainerConfigTest\SharedTestTrait` as `final`.

### Deprecated

- Nothing.

### Removed

- [#7](https://github.com/zendframework/zend-container-config-test/pull/7)
  removes the data provider methods from all test traits, referencing those in
  the new `Zend\ContainerConfigTest\Helper\Provider` class instead.

- [#7](https://github.com/zendframework/zend-container-config-test/pull/7)
  removes `Zend\ContainerConfigTest\AllTestTrait`; compose the traits you wish
  to test against manually.

- [#7](https://github.com/zendframework/zend-container-config-test/pull/7)
  removes `Zend\ContainerConfigTest\ExpressiveTestTrait`; compose the traits you
  wish to test against manually, or extend the class
  `Zend\ContainerConfigTest\AbstractExpressiveContainerConfigTest`.

### Fixed

- Nothing.

## 0.1.0 - 2018-04-10

Initial public release.

### Added

- Everything.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
