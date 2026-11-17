# 0001. One package: a framework-free core and a Laravel bridge

- Status: Accepted
- Date: 2026-11-17

## Context

`fit0/gs1` brings GS1 to PHP: check digits for GTIN and SSCC, a parser for GS1-128 element strings, and labels in ZPL and PDF. Most of it has nothing to do with a framework. Part of it is wiring for Laravel: a service provider, a facade, a publishable config, a Blade component and an Artisan command.

Estiba WMS already holds the first pieces (the check digit, `Gtin`, `Sscc`, its own Code 128 encoder with FNC1, and the ZPL and PDF renderers), written for one project. This package is where they become reusable.

## Options considered

1. **One package with a core and a bridge in the same repository.** One repository, one entry in Packagist, one version number. The core and the bridge live in different namespaces, and a test keeps the core from using Illuminate.
2. **Two packages in a monorepo, split into two repositories for Packagist** (`fit0/gs1` and `fit0/laravel-gs1`). The cleanest separation, since the core cannot use Laravel by construction. It costs a split job, two versions to release and two issue trackers for a package this size.
3. **Only a Laravel package.** The fastest, but a Symfony or plain PHP project could not use it without installing Laravel.

## Decision

Option 1.

- The core is `Fit0\Gs1` (`src/`), and the bridge is `Fit0\Gs1\Laravel` (`src/Laravel/`).
- `composer.json` requires only PHP. `laravel/framework` is in `suggest`, and Laravel finds the provider by itself through `extra.laravel`.
- An architecture test (Pest) fails when anything outside `Fit0\Gs1\Laravel` uses a class of `Illuminate`. A CI job installs the package **without** Testbench, checks that `vendor/illuminate` does not exist and runs the core tests. Together they stand in for the separation that two packages would give.
- The demo (a Laravel app with Inertia and Vue) lives in `demo/` and is left out of the published package with `export-ignore`.
- **Estiba does not depend on this package.** The package is extracted from Estiba, and Estiba keeps its own code. A flagship project should not need a satellite to build, and the two can drift where their needs differ (Estiba's check digit takes only digits it already validated; the package must reject anything else).

## Consequences

- One repository to release, test and document.
- The core and the bridge share a version. A change that only the bridge needs still bumps the version of the core.
- If the bridge ever grows enough to deserve its own life, it can be moved to a second package. Its namespace already says where the line is.
- The architecture test is the only wall between the two, so it must never be weakened to make a change pass.
