# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.3.1 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.3.0 - 2020-12-05


-----

### Release Notes for [0.3.0](https://github.com/laminas/laminas-container-config-test/milestone/1)



### 0.3.0

- Total issues resolved: **0**
- Total pull requests resolved: **1**
- Total contributors: **1**

#### Enhancement

 - [3: Bump minimum supported PHP version to 7.3 and add support for PHP 8.0](https://github.com/laminas/laminas-container-config-test/pull/3) thanks to @boesing

## 0.2.3 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.2 - 2019-09-06

### Added

- [zendframework/zend-container-config-test#11](https://github.com/zendframework/zend-container-config-test/pull/11) adds support for PHP 7.3.

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
