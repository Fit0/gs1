<?php

declare(strict_types=1);

namespace Fit0\Gs1;

use Fit0\Gs1\Exception\InvalidDigits;

/**
 * The check digit that ends every GS1 number (GTIN, SSCC…): from the right, the digits weigh
 * 3, 1, 3, 1… and the check digit completes the sum to a multiple of ten.
 */
final readonly class Gs1CheckDigit
{
    /**
     * @throws InvalidDigits when the value is empty or has anything but the digits 0 to 9
     */
    public static function for(string $digitsWithoutCheckDigit): int
    {
        if (1 !== preg_match('/^\d+$/D', $digitsWithoutCheckDigit)) {
            throw InvalidDigits::forValue($digitsWithoutCheckDigit);
        }

        $sum = 0;
        $weight = 3;

        foreach (array_reverse(str_split($digitsWithoutCheckDigit)) as $digit) {
            $sum += $weight * (int) $digit;
            $weight = 4 - $weight;
        }

        return (10 - $sum % 10) % 10;
    }

    /**
     * Whether a number that already ends in its check digit has the right one. A value that is not
     * made of at least two digits is not valid, and it does not throw.
     */
    public static function isValid(string $digitsWithCheckDigit): bool
    {
        return 1 === preg_match('/^\d{2,}$/D', $digitsWithCheckDigit)
            && self::for(substr($digitsWithCheckDigit, 0, -1)) === (int) substr($digitsWithCheckDigit, -1);
    }
}
