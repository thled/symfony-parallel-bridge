# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.2] - 2021-01-18

### Fixed
- Fix async processing

## [1.1.1] - 2021-01-18

### Fixed
- Fix sync processing

### Added
- TODO.md

## [1.1.0] - 2021-01-18

### Added
- Optional synchronous execution when using "0" as worker amount.

### Fixed
- links in CHANGELOG.md

## [1.0.2] - 2020-12-30

### Changed

- Readme recommends usage of the interface.

### Fixed

- Autowiring for newly introduced `Publicplan\ParallelBridge\PromiseWaitInterface`.

## [1.0.1] - 2020-12-30

### Added

- Interface for `Publicplan\ParallelBridge\PromiseWait` as the entry point to the lib.

## [1.0.0] - 2020-12-14

### Initial Features

- Bridge ParallelMap from Amphp with Symfony context
- additional Arguments
- strict phpstan checked
- unit tested
- code style fixed with php-cs-fixer
- support for php 7.3, 7.4, 8.0 and Symfony 4.4, 5.2

[unreleased]: https://github.com/thled/symfony-parallel-bridge/compare/v1.1.2...HEAD
[1.1.2]: https://github.com/thled/symfony-parallel-bridge/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/thled/symfony-parallel-bridge/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/thled/symfony-parallel-bridge/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/thled/symfony-parallel-bridge/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/thled/symfony-parallel-bridge/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/thled/symfony-parallel-bridge/releases/tag/v1.0.0
