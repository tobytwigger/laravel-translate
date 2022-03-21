## Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v0.3] - (21/03/2022)

### Fixed
- Error thrown with PHP 8 due to an optional parameter coming before a required one.

### Added
- Support for php 8.1 and laravel 9

### Removed
- Removed support for php 7.3

## [v0.2] - (12/01/2021)

### Added
- static getCacheKey function in the CacheInterceptor to get the key of a given translation.
- [Issue 1](https://github.com/tobytwigger/laravel-translate/issues/1) - Clear the cache when a database translation is saved.
- [Issue 10](https://github.com/tobytwigger/laravel-translate/issues/10) - Changed the _translate API to a get request for proper caching.
- [Issue 7](https://github.com/tobytwigger/laravel-translate/issues/7) - Added supported languages key to config.
- [Issue 15](https://github.com/tobytwigger/laravel-translate/issues/15) - Added implementation documentation
- [Issue 6](https://github.com/tobytwigger/laravel-translate/issues/6) - Added Free Google Translate translator.
- [Issue 6](https://github.com/tobytwigger/laravel-translate/issues/6) - Added Stack translator.
- Added support for php 8

### Removed
- Removed support for php 7.2

## [v0.1] - (09/09/2020)

> This is a pre-release. It is still under construction, although it is mostly in a final state.

### Added
- Initial release

[Unreleased]: https://github.com/tobytwigger/laravel-translate/compare/v0.2...HEAD
[v0.2]: https://github.com/tobytwigger/laravel-translate/compare/v0.1...v0.2
[v0.1]: https://github.com/bristol-su/support/releases/tag/v0.1
