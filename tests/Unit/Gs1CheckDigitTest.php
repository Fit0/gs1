<?php

declare(strict_types=1);

use Fit0\Gs1\Gs1CheckDigit;

it('computes the check digit of a GS1 number', function (string $digits, int $expected): void {
    expect(Gs1CheckDigit::for($digits))->toBe($expected);
})->with([
    // Published examples: 5901234123457, 96385074 and 036000291452.
    'GTIN-13' => ['590123412345', 7],
    'GTIN-8' => ['9638507', 4],
    'UPC-A' => ['03600029145', 2],
    // Worked out apart from the class, to catch a change of behaviour.
    'GTIN-14' => ['1234567890123', 1],
    'SSCC' => ['34012345000000012', 3],
]);

it('gives 0 when the weighted sum is already a multiple of ten', function (): void {
    // From the right: 7 weighs 3 and 9 weighs 1, so the sum is 21 + 9 = 30.
    expect(Gs1CheckDigit::for('97'))->toBe(0);
});
