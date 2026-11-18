<?php

declare(strict_types=1);

namespace Fit0\Gs1\Exception;

/**
 * Every serial that the company prefix leaves room for has been used.
 */
final class SsccSerialsExhausted extends \OverflowException implements Gs1Exception
{
    public static function forPrefix(string $companyPrefix): self
    {
        return new self(\sprintf('No SSCC serial is left for the company prefix %s.', $companyPrefix));
    }
}
