<?php

declare(strict_types=1);

namespace Fit0\Gs1\Exception;

/**
 * A check digit was asked for something that is not a string of digits.
 */
final class InvalidDigits extends \InvalidArgumentException implements Gs1Exception
{
    public static function forValue(string $value): self
    {
        return new self(\sprintf('"%s" is not a string of digits (0 to 9) and has no GS1 check digit.', $value));
    }
}
