<?php

declare(strict_types=1);

namespace Fit0\Gs1;

use Fit0\Gs1\Exception\InvalidGtin;

/**
 * A GS1 barcode number: GTIN-8, GTIN-12 (UPC), GTIN-13 (EAN) or GTIN-14, whose last digit is a check digit.
 *
 * The same item can be printed with different lengths (a UPC is an EAN with a leading zero), so two
 * GTINs are equal when they match once padded to 14 digits.
 */
final readonly class Gtin implements \Stringable
{
    private const array LENGTHS = [8, 12, 13, 14];

    private function __construct(
        public string $value,
        public string $normalized,
    ) {
    }

    /**
     * @throws InvalidGtin when the length is not 8, 12, 13 or 14, or it is not digits, or the check digit is wrong
     */
    public static function fromString(string $value): self
    {
        $digits = trim($value);

        if (!\in_array(\strlen($digits), self::LENGTHS, true) || !Gs1CheckDigit::isValid($digits)) {
            throw InvalidGtin::forValue($value);
        }

        return new self($digits, str_pad($digits, 14, '0', \STR_PAD_LEFT));
    }

    public function equals(self $other): bool
    {
        return $this->normalized === $other->normalized;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
