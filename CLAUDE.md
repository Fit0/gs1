# CLAUDE.md

Guidance for Claude Code when working in this repository. Read it before changing anything.

`fit0/gs1` is an open-source PHP package for GS1: check digits, GTIN and SSCC, a GS1-128 element string parser, ZPL and PDF labels, and a Laravel bridge. It is part of the author's professional portfolio, next to Estiba WMS, and reviewers will read this code. Code quality, clear history and documentation matter as much as features.

## Tech stack

| Layer | Technology |
|---|---|
| Language | PHP 8.3 and newer (`composer.json` requires `^8.3`) |
| Tests | Pest 4 on PHP 8.3, Pest 5 on 8.4 and 8.5 (Composer picks), architecture tests with Pest's `arch()` |
| Laravel tests | Orchestra Testbench 10 (Laravel 12) and 11 (Laravel 13) |
| Static analysis and style | PHPStan 2 (max level, strict rules), PHP-CS-Fixer, Rector 2 |
| CI | GitHub Actions: core without Laravel, and a matrix of PHP 8.3 to 8.5 by Laravel 12 and 13 |

The runtime has **no dependency**: `require` holds only PHP. Do not add a dependency without asking first, and never to `require` without a long reason in an ADR. When one is approved, add it here.

## Architecture

One package, two namespaces ([ADR 0001](docs/adr/0001-one-package-a-core-and-a-laravel-bridge.md)):

```
src/
├── *.php        The core, `Fit0\Gs1`: plain PHP, no framework
└── Laravel/     The bridge, `Fit0\Gs1\Laravel`: provider, facade, config, Blade component, Artisan command
```

- **The core never uses `Illuminate`.** `tests/Architecture/ArchitectureTest.php` fails if it does, and a CI job runs the core with Laravel not installed. Never weaken that rule to make a change pass: move the code to `Laravel/`.
- The bridge depends on the core, never the other way round.
- **Estiba WMS does not depend on this package, and this package does not depend on Estiba.** Code comes from Estiba by extraction: copy it, harden it for public use (reject what it did not need to reject) and give it its own tests.
- Every decision about the structure, the versions or a dependency gets an ADR in `docs/adr/` (start from `docs/adr/template.md`).

## Code conventions

- `declare(strict_types=1);` in every file. Classes are `final` and services and value objects `readonly`.
- Value objects for GS1 concepts (`Gtin`, `Sscc`, an application identifier). No primitive obsession.
- Strict comparisons only (`===`). No short ternary. Only booleans in conditions.
- Native types everywhere. PHPDoc only for what native types cannot express.
- Methods do one thing: aim for under 20 lines, refactor beyond 30.
- Errors are specific exceptions named after what went wrong (`InvalidGtin`), never a generic `\Exception`.
- Code, comments, identifiers, commit messages and documentation are in **English**.
- **Do not add `@phpstan-ignore`, baselines or `ignoreErrors`.** Fix the code. If something truly cannot be fixed, stop and explain why. PHPStan analyses `src/` only, because the tests are Pest closures.
- Imports: one `use` per class, ordered class, function, const. PHP-CS-Fixer enforces it.

## Tests

- Every rule has a test, written with Pest (`it(...)`, datasets for the cases).
- Test vectors come from a published source (the GS1 General Specifications, a standard's example) and say which. A vector worked out by the code under test proves nothing: work it out apart and say so in the test.
- Core tests live in `tests/Unit/`, architecture tests in `tests/Architecture/` (both in the `Core` suite, which runs without Laravel), and Laravel tests in `tests/Laravel/` (from E11-04).

## Commands and workflows

Everything runs inside Docker. Use `make` from the repository root (`make` alone lists all targets). `PHP=8.3 make ...` picks the version of PHP; after switching, run `make update PHP=8.3` once, because the lock file is not committed.

| Command | Purpose |
|---|---|
| `make install` / `make update` | Install / resolve again the dependencies |
| `make test` | Pest |
| `make stan` | PHPStan |
| `make cs` / `make rector` | Style and refactoring checks (dry run) |
| `make fix` | Apply Rector and PHP-CS-Fixer |
| `make qa` | All checks. **Must pass before proposing a commit.** |

### Git workflow

- Commits follow Conventional Commits, enforced by `.githooks/commit-msg` (`git config core.hooksPath .githooks`). Types and scopes are in `CONTRIBUTING.md`.
- Commit messages end with a `Co-Authored-By` line when Claude contributed.
- **The author commits and pushes.** When a task is done, run `make qa`, then propose the commit message and stop. Do not commit, push, create branches on the remote or open PRs unless explicitly asked.
- Never send the author's email address in a `User-Agent` header or in any request.

## Constraints

- No secrets, tokens or real customer data in the repository, in fixtures or in tests.
- The published package is the core and the bridge only: `tests/`, `docs/`, `demo/` and the tooling are left out with `export-ignore` in `.gitattributes`.
- `LICENSE` is legal text: do not edit it.
- Do not commit `composer.lock` (a library is tested against the newest versions its constraints allow, [ADR 0002](docs/adr/0002-supported-versions-and-the-test-matrix.md)).
