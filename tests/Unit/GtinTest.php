<?php

declare(strict_types=1);

use Fit0\Gs1\Exception\InvalidGtin;
use Fit0\Gs1\Gtin;

it('accepts a GTIN with a correct check digit and pads it to 14 digits', function (string $input, string $normalized): void {
    $gtin = Gtin::fromString($input);

    expect($gtin->value)->toBe(trim($input))
        ->and($gtin->normalized)->toBe($normalized)
        ->and((string) $gtin)->toBe(trim($input));
})->with([
    'GTIN-8' => ['96385074', '00000096385074'],
    'GTIN-12 (UPC)' => ['036000291452', '00036000291452'],
    'GTIN-13 (EAN)' => ['4006381333931', '04006381333931'],
    'GTIN-14' => ['10614141000415', '10614141000415'],
    'spaces around are removed' => [' 4006381333931 ', '04006381333931'],
    'a line break around is removed' => ["4006381333931\n", '04006381333931'],
]);

it('rejects a GTIN that breaks the format or the check digit', function (string $input): void {
    expect(fn (): Gtin => Gtin::fromString($input))->toThrow(InvalidGtin::class);
})->with([
    'wrong check digit' => ['4006381333932'],
    'empty' => [''],
    'too short' => ['1234567'],
    'length 10' => ['4006381333'],
    'too long' => ['400638133393100'],
    'letters' => ['40063813339A1'],
    'dashes' => ['4006-381-333-931'],
]);

it('says which value it rejected', function (): void {
    expect(fn (): Gtin => Gtin::fromString('4006381333932'))
        ->toThrow(InvalidGtin::class, '"4006381333932" is not a valid GTIN');
});

it('treats a UPC and the EAN with a leading zero as the same GTIN', function (): void {
    $upc = Gtin::fromString('036000291452');

    expect($upc->equals(Gtin::fromString('0036000291452')))->toBeTrue()
        ->and($upc->equals(Gtin::fromString('4006381333931')))->toBeFalse();
});
