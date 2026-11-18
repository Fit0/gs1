<?php

declare(strict_types=1);

use Fit0\Gs1\Exception\Gs1Exception;

// The package has one core and one bridge (ADR 0001). The core must work with Laravel absent, so nothing
// outside Fit0\Gs1\Laravel may use it.
arch('the core does not use Laravel')
    ->expect('Fit0\Gs1')
    ->not->toUse('Illuminate')
    ->ignoring('Fit0\Gs1\Laravel');

arch('every file of the package declares strict types')
    ->expect('Fit0\Gs1')
    ->toUseStrictTypes();

arch('every exception of the package can be caught as a Gs1Exception')
    ->expect('Fit0\Gs1\Exception')
    ->toImplement(Gs1Exception::class)
    ->ignoring(Gs1Exception::class);
