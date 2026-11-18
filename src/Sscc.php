<?php

declare(strict_types=1);

namespace Fit0\Gs1;

use Fit0\Gs1\Exception\InvalidSscc;
use Fit0\Gs1\Exception\SsccSerialsExhausted;

/**
 * The Serial Shipping Container Code that identifies one logistic unit in the whole supply chain: 18
 * digits, the last one a GS1 check digit.
 *
 * It is the extension digit, the GS1 company prefix of whoever ships, a serial reference that company
 * never repeats, and the check digit. The package does not know which prefixes GS1 has licensed: it
 * only checks the structure.
 */
final readonly class Sscc implements \Stringable
{
    public const int LENGTH = 18;

    private function __construct(public string $value)
    {
    }

    /**
     * @throws InvalidSscc when it is not 18 digits or the check digit is wrong
     */
    public static function fromString(string $value): self
    {
        $digits = trim($value);

        if (self::LENGTH !== \strlen($digits) || !Gs1CheckDigit::isValid($digits)) {
            throw InvalidSscc::forValue($value);
        }

        return new self($digits);
    }

    /**
     * Builds the SSCC of the next serial of a company: the serial is left-padded with zeros to fill
     * the 17 digits that come before the check digit.
     *
     * @throws InvalidSscc          when the extension digit is not 0 to 9, the prefix is not digits or leaves
     *                              no room for a serial, or the serial is negative
     * @throws SsccSerialsExhausted when the serial needs more digits than the prefix leaves
     */
    public static function compose(int $extensionDigit, string $companyPrefix, int $serial): self
    {
        $prefixLength = \strlen($companyPrefix);

        // The extension digit, the prefix, at least one digit of serial and the check digit make 18.
        if ($extensionDigit < 0 || $extensionDigit > 9
            || 1 !== preg_match('/^\d+$/D', $companyPrefix)
            || $prefixLength > self::LENGTH - 3
            || $serial < 0
        ) {
            throw InvalidSscc::forParts($extensionDigit, $companyPrefix, $serial);
        }

        $serialLength = self::LENGTH - 2 - $prefixLength;
        if (\strlen((string) $serial) > $serialLength) {
            throw SsccSerialsExhausted::forPrefix($companyPrefix);
        }

        $body = $extensionDigit.$companyPrefix.str_pad((string) $serial, $serialLength, '0', \STR_PAD_LEFT);

        return new self($body.Gs1CheckDigit::for($body));
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
