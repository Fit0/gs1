# Contributing to fit0/gs1

## Workflow

1. Open an issue (feature or bug) describing the change.
2. Create a branch from `main`: `<type>/<short-description>`, for example `feat/gtin-value-object`.
3. Commit using Conventional Commits. Enable the hook with `git config core.hooksPath .githooks`.
4. Run `make qa` locally, then open a pull request. The PR title must follow Conventional Commits. All CI jobs must pass before merging.
5. Squash-merge into `main`. The squash commit message must also follow Conventional Commits.

## Commit messages

Format: `<type>(<scope>): <description>`

| Type | Use for |
|---|---|
| `feat` | A new feature |
| `fix` | A bug fix |
| `refactor` | Code change that neither fixes a bug nor adds a feature |
| `test` | Adding or fixing tests |
| `docs` | Documentation only |
| `build` | Dependencies, Docker, Composer |
| `ci` | CI configuration |
| `perf` | Performance improvement |
| `style` | Formatting, no code change |
| `chore` | Maintenance that fits none of the above |

Scopes: `core`, `parser`, `labels`, `laravel`, `demo`, `docs`.

Examples: `feat(core): add the Gtin value object`, `fix(parser): read a date with day 00`.

## Decisions

Every decision about the structure, the supported versions or a dependency gets an ADR in `docs/adr/`, starting from `docs/adr/template.md`.
