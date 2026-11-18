<?php

declare(strict_types=1);

use Fit0\Gs1\Exception\InvalidDigits;
use Fit0\Gs1\Gs1CheckDigit;

it('computes the check digit of a GS1 number', function (string $digits, int $expected): void {
    expect(Gs1CheckDigit::for($digits))->toBe($expected);
})->with([
    // Well-known EAN-13, GTIN-8 and UPC-A numbers (5901234123457, 96385074, 036000291452), and the
    // algorithm agrees with an independent implementation. Not checked against a GS1 document.
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

it('rejects what is not a string of digits', function (string $value): void {
    expect(fn (): int => Gs1CheckDigit::for($value))->toThrow(InvalidDigits::class);
})->with([
    'empty' => [''],
    'letters' => ['12A4'],
    'a space' => ['12 4'],
    'a minus sign' => ['-123'],
    'a decimal point' => ['1.5'],
    // `$` in a regular expression also matches before a final line break: the check must not.
    'a final line break' => ["1234\n"],
    'full-width digits' => ['１２３'],
]);

it('says whether a number ends in the right check digit', function (string $number, bool $expected): void {
    expect(Gs1CheckDigit::isValid($number))->toBe($expected);
})->with([
    'GTIN-13' => ['5901234123457', true],
    'GTIN-8' => ['96385074', true],
    'wrong check digit' => ['5901234123458', false],
    'only the check digit' => ['7', false],
    'empty' => ['', false],
    'letters' => ['59012341234X7', false],
    'a final line break' => ["5901234123457\n", false],
]);
