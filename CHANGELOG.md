# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.2.1 - 2018-04-12

### Added

- Nothing.

### Changed

- [zendframework/zend-container-config-test#9](https://github.com/zendframework/zend-container-config-test/pull/9) changes several test asset function names
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

- [zendframework/zend-container-config-test#7](https://github.com/zendframework/zend-container-config-test/pull/7) adds
  the class `Laminas\ContainerConfigTest\Helper\Provider`, containing data
  providers consumed by the various test traits.

### Changed

- [zendframework/zend-container-config-test#7](https://github.com/zendframework/zend-container-config-test/pull/7) marks
  all methods in `Laminas\ContainerConfigTest\SharedTestTrait` as `final`.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-container-config-test#7](https://github.com/zendframework/zend-container-config-test/pull/7)
  removes the data provider methods from all test traits, referencing those in
  the new `Laminas\ContainerConfigTest\Helper\Provider` class instead.

- [zendframework/zend-container-config-test#7](https://github.com/zendframework/zend-container-config-test/pull/7)
  removes `Laminas\ContainerConfigTest\AllTestTrait`; compose the traits you wish
  to test against manually.

- [zendframework/zend-container-config-test#7](https://github.com/zendframework/zend-container-config-test/pull/7)
  removes `Laminas\ContainerConfigTest\MezzioTestTrait`; compose the traits you
  wish to test against manually, or extend the class
  `Laminas\ContainerConfigTest\AbstractMezzioContainerConfigTest`.

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
