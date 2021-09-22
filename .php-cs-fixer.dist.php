<?php

declare(strict_types=1);

use VisualCraft\PhpCsFixerConfig;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->append([
        __DIR__ . '/.php-cs-fixer.dist.php',
    ])
;

$config = PhpCsFixerConfig\Factory::fromRuleSet(new PhpCsFixerConfig\RuleSet\Php80());
$config
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
;

return $config;
