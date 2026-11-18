# fit0/gs1

GS1 for PHP: check digits for GTIN and SSCC, a parser for GS1-128 element strings, and labels in ZPL and PDF, with a bridge for Laravel.

> **Status: work in progress, not released.** Only the check digit and the GTIN and SSCC value objects exist so far. The rest comes in the order of the table below, and nothing is published on Packagist until `v1.0.0`.

The core needs only PHP. The Laravel bridge is optional and loads by itself when Laravel is there. The package is extracted from the GS1 code of [Estiba WMS](https://github.com/Fit0/estiba-wms), a warehouse management system that makes the SSCC labels of its parcels.

## What is there

```php
use Fit0\Gs1\Gs1CheckDigit;
use Fit0\Gs1\Gtin;
use Fit0\Gs1\Sscc;

Gs1CheckDigit::for('590123412345');   // 7, so the GTIN-13 is 5901234123457
Gs1CheckDigit::isValid('5901234123457'); // true

Gtin::fromString('036000291452')->equals(Gtin::fromString('0036000291452')); // true: a UPC is an EAN with a zero

Sscc::compose(0, '5012345', 123456789)->value; // '050123451234567893'
Sscc::fromString('050123451234567894');       // throws Fit0\Gs1\Exception\InvalidSscc
```

Every exception the package throws implements `Fit0\Gs1\Exception\Gs1Exception`.

| Piece | Status |
|---|---|
| Check digit, `Gtin` and `Sscc` | Done |
| GS1-128 element string parser | Next |
| ZPL and PDF labels with GS1-128 | Planned |
| Laravel bridge: provider, facade, config, Blade component, Artisan command | Planned |
| Demo app (Laravel, Inertia and Vue 3) | Planned |

## Requirements

PHP 8.3 or newer. The bridge supports Laravel 12 and 13. See [ADR 0002](docs/adr/0002-supported-versions-and-the-test-matrix.md) for what is tested.

## Development

Everything runs in Docker. `make` lists the commands.

```bash
make install   # dependencies, on PHP 8.4
make qa        # style, Rector, PHPStan and the tests
make update qa PHP=8.3   # the same on another version of the matrix
```

The decisions are in [docs/adr](docs/adr/), and the rules for working in the repository in [CONTRIBUTING.md](CONTRIBUTING.md).

## License

MIT.
