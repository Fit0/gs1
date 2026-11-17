<?php

declare(strict_types=1);

// The package has one core and one bridge (ADR 0001). The core must work with Laravel absent, so nothing
// outside Fit0\Gs1\Laravel may use it.
arch('the core does not use Laravel')
    ->expect('Fit0\Gs1')
    ->not->toUse('Illuminate')
    ->ignoring('Fit0\Gs1\Laravel');

arch('every file of the package declares strict types')
    ->expect('Fit0\Gs1')
    ->toUseStrictTypes();
