<?php

declare(strict_types=1);

namespace Fit0\Gs1\Exception;

/**
 * A GTIN has the wrong length, has characters that are not digits or fails the GS1 check digit.
 */
final class InvalidGtin extends \InvalidArgumentException implements Gs1Exception
{
    public static function forValue(string $value): self
    {
        return new self(\sprintf('"%s" is not a valid GTIN. Expected 8, 12, 13 or 14 digits with a correct GS1 check digit.', $value));
    }
}
