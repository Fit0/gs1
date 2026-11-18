<?php

declare(strict_types=1);

namespace Fit0\Gs1\Exception;

/**
 * Not 18 digits with the GS1 check digit, or parts that cannot make an SSCC.
 */
final class InvalidSscc extends \InvalidArgumentException implements Gs1Exception
{
    public static function forValue(string $value): self
    {
        return new self(\sprintf('"%s" is not a valid SSCC. Expected 18 digits with a correct GS1 check digit.', $value));
    }

    public static function forParts(int $extensionDigit, string $companyPrefix, int $serial): self
    {
        return new self(\sprintf(
            'Extension digit %d, company prefix "%s" and serial %d cannot make an SSCC. The extension digit is 0 to 9, the prefix is digits and leaves room for a serial, and the serial is not negative.',
            $extensionDigit,
            $companyPrefix,
            $serial,
        ));
    }
}
