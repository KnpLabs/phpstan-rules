# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.0](https://github.com/KnpLabs/phpstan-rules/compare/v0.1.0...v0.2.0) (2026-07-27)


### Features

* **psr20:** ban relative date strings in DateTime/DateTimeImmutable constructors ([#13](https://github.com/KnpLabs/phpstan-rules/issues/13)) ([d2d9255](https://github.com/KnpLabs/phpstan-rules/commit/d2d9255fb07454acd2b6e09793516f7b0a4122ae))

## [0.1.0](https://github.com/KnpLabs/phpstan-rules/compare/v0.0.1...v0.1.0) (2026-07-15)


### Features

* add GitHub Actions workflow for automated PHP releases ([c656aea](https://github.com/KnpLabs/phpstan-rules/commit/c656aea0e284ae832087759a5efe9de9662b0b3b))

## [0.0.1] — 2026-07-15

### Added

- `clock.disallowDateTimeNow` rule — prevents instantiating `DateTime` or `DateTimeImmutable` with the current time (PSR-20 compliance).
- `clock.disallowTimeFunctions` rule — prevents calling `time()` or `date()` without an explicit timestamp argument (PSR-20 compliance).
- Automatic Packagist publishing on GitHub release via `publish.yml` workflow.
- CI matrix across PHP 8.2, 8.3, 8.4, and 8.5.
[0.0.1]: https://github.com/KnpLabs/phpstan-rules/releases/tag/v0.0.1
