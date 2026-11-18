<?php

declare(strict_types=1);

use Fit0\Gs1\Exception\InvalidSscc;
use Fit0\Gs1\Exception\SsccSerialsExhausted;
use Fit0\Gs1\Sscc;

// Published by GS1 UK ("How to create logistics labels and SSCCs"): the extension digit, a company prefix of
// 7, 8, 9 and 10 digits, the serial reference that fills the rest, and the check digit.
const PUBLISHED_SSCCS = [
    '7-digit prefix' => ['050123451234567893', 0, '5012345', 123456789],
    '8-digit prefix' => ['050423456123456781', 0, '50423456', 12345678],
    '9-digit prefix' => ['050523456712345679', 0, '505234567', 1234567],
    '10-digit prefix' => ['050623456781234569', 0, '5062345678', 123456],
];

it('accepts an SSCC whose check digit is right', function (string $sscc): void {
    expect(Sscc::fromString($sscc)->value)->toBe($sscc)
        ->and(Sscc::fromString(" {$sscc} ")->value)->toBe($sscc);
})->with(array_map(static fn (array $case): array => [$case[0]], PUBLISHED_SSCCS));

it('rejects anything that is not an SSCC with its check digit', function (string $value): void {
    expect(fn (): Sscc => Sscc::fromString($value))->toThrow(InvalidSscc::class);
})->with([
    'wrong check digit' => ['050123451234567894'],
    'too short' => ['05012345123456789'],
    'too long' => ['0501234512345678930'],
    'not digits' => ['05012345123456789X'],
    'empty' => [''],
]);

it('composes the SSCC of a serial of the company', function (string $sscc, int $extension, string $prefix, int $serial): void {
    expect(Sscc::compose($extension, $prefix, $serial)->value)->toBe($sscc);
})->with(PUBLISHED_SSCCS);

it('pads the serial with zeros to fill the code and ends with its check digit', function (): void {
    $sscc = Sscc::compose(0, '8400000', 123456789);

    expect($sscc->value)->toHaveLength(18)
        ->and(substr($sscc->value, 0, 16))->toBe('0840000012345678')
        ->and(Sscc::fromString($sscc->value)->value)->toBe($sscc->value);
});

it('leaves room for fewer serials when the prefix is longer', function (): void {
    expect(Sscc::compose(0, '8400000123', 999999)->value)->toHaveLength(18)
        ->and(fn (): Sscc => Sscc::compose(0, '8400000123', 10_000_000))->toThrow(SsccSerialsExhausted::class);
});

it('has the nine nines as the last serial of a seven-digit prefix', function (): void {
    expect(Sscc::compose(0, '8400000', 999_999_999)->value)->toHaveLength(18)
        ->and(fn (): Sscc => Sscc::compose(0, '8400000', 1_000_000_000))->toThrow(SsccSerialsExhausted::class);
});

it('rejects parts that cannot make an SSCC', function (int $extension, string $prefix, int $serial): void {
    expect(fn (): Sscc => Sscc::compose($extension, $prefix, $serial))->toThrow(InvalidSscc::class);
})->with([
    'extension above 9' => [10, '8400000', 1],
    'negative extension' => [-1, '8400000', 1],
    'prefix with letters' => [0, '84A0000', 1],
    'empty prefix' => [0, '', 1],
    'a final line break in the prefix' => [0, "8400000\n", 1],
    // 15 digits is the longest prefix that leaves one digit of serial; 16 leaves none.
    'prefix with no room for a serial' => [0, '8400000123456789', 1],
    'negative serial' => [0, '8400000', -1],
]);

it('accepts the longest prefix that leaves one digit of serial', function (): void {
    expect(Sscc::compose(0, '840000012345678', 9)->value)->toHaveLength(18);
});

it('compares two SSCCs by their digits', function (): void {
    $first = Sscc::compose(0, '8400000', 1);

    expect($first->equals(Sscc::fromString('084000000000000017')))->toBeTrue()
        ->and($first->equals(Sscc::compose(0, '8400000', 2)))->toBeFalse()
        ->and((string) $first)->toBe('084000000000000017');
});
