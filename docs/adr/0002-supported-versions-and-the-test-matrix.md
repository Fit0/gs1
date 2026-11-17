# 0002. Supported versions and the test matrix

- Status: Accepted
- Date: 2026-11-17

## Context

A package says which versions of PHP and Laravel it works on, and that promise is only worth what the tests behind it show.

What was true on 2026-11-17, from the pages of each project:

- Laravel 13 needs PHP 8.3 or newer, and Laravel 12 runs on PHP 8.2 to 8.5 ([Laravel release notes](https://laravel.com/docs/master/releases)). Laravel 14 will need PHP 8.4.
- Laravel 11 stopped receiving security fixes on 2026-03-12.
- Orchestra Testbench 10 goes with Laravel 12, and Testbench 11 with Laravel 13.
- Pest 5 needs PHP 8.4 and PHPUnit 13. On PHP 8.3 the newest Pest is the 4 series.

## Options considered

1. **PHP 8.4 and newer.** The same as Estiba, and Pest 5 would run everywhere. It leaves out anyone who runs Laravel 12 or 13 on PHP 8.3, which is allowed by both.
2. **PHP 8.3 and newer, Laravel 12 and 13.** It covers what Laravel supports today, as long as the tests can run on PHP 8.3 too.
3. **PHP 8.2 and newer, Laravel 11 to 13.** Laravel 13 does not run on 8.2, and Laravel 11 has no security support left. It would mean testing versions nobody should be on.

## Decision

Option 2.

- `composer.json` requires `php: ^8.3`.
- The test tools accept both lines, `pestphp/pest: ^4.0 || ^5.0` and `orchestra/testbench: ^10.0 || ^11.0`, and Composer picks the one each PHP version allows. The same test files run under Pest 4 (PHP 8.3) and Pest 5 (PHP 8.4 and 8.5).
- CI runs, on PHP 8.3, 8.4 and 8.5:
  - the **core** without Laravel installed, and
  - the whole suite on **Laravel 12** (Testbench 10) and **Laravel 13** (Testbench 11).
- The lock file is not committed: a library is tested against the newest versions its constraints allow, and a lock file would hide a break caused by a new release.
- Each version dropped, or raised, goes in the changelog. When Laravel 14 comes out, the minimum PHP becomes 8.4 with a new major version of the package.

## Consequences

- The promise is checked by 3 core jobs and 6 Laravel jobs in every pull request.
- The first run of the matrix is also the first time Composer resolves these constraints together. If a pair does not resolve (a Pest or a Testbench constraint that does not fit a PHP version), the matrix shows it and this ADR is corrected.
- Local runs use one PHP version at a time: `make qa PHP=8.3`, after `make update PHP=8.3`.
