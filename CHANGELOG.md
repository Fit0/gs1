# Changelog

All notable changes to this project are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the project follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added

- `Gs1CheckDigit`, `Gtin` and `Sscc`, extracted from Estiba WMS and hardened for public use ([ADR 0003](docs/adr/0003-extracting-the-gs1-code-from-estiba.md)): the check digit rejects anything that is not digits and has `isValid()`, `Gtin` accepts GTIN-8, 12, 13 and 14 and compares them padded to 14 digits, and `Sscc` validates and composes the 18-digit code.
- `Gs1Exception`, implemented by every exception the package throws.
- The project: Pest, PHPStan at max level, PHP-CS-Fixer, Rector, Docker commands and CI on PHP 8.3, 8.4 and 8.5, with Laravel 12 and 13 ([ADR 0001](docs/adr/0001-one-package-a-core-and-a-laravel-bridge.md), [ADR 0002](docs/adr/0002-supported-versions-and-the-test-matrix.md)).
