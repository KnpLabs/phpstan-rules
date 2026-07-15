# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.0.1] — 2026-07-15

### Added

- `clock.disallowDateTimeNow` rule — prevents instantiating `DateTime` or `DateTimeImmutable` with the current time (PSR-20 compliance).
- `clock.disallowTimeFunctions` rule — prevents calling `time()` or `date()` without an explicit timestamp argument (PSR-20 compliance).
- Automatic Packagist publishing on GitHub release via `publish.yml` workflow.
- CI matrix across PHP 8.2, 8.3, 8.4, and 8.5.

[Unreleased]: https://github.com/KnpLabs/phpstan-rules/compare/v0.0.1...HEAD
[0.0.1]: https://github.com/KnpLabs/phpstan-rules/releases/tag/v0.0.1
