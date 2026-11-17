<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__.'/src', __DIR__.'/tests'])
    ->append([__FILE__, __DIR__.'/rector.php'])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setCacheFile(__DIR__.'/var/cache/.php-cs-fixer.cache')
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP8x3Migration' => true,
        'declare_strict_types' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'final_class' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'phpdoc_to_comment' => false,
        // Pest binds $this (the TestCase) to each test closure, and a static closure cannot be bound.
        'static_lambda' => false,
    ])
    ->setFinder($finder)
;
