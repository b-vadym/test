<?php

declare(strict_types=1);

use VisualCraft\PhpCsFixerConfig;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/migrations')
    ->in(__DIR__ . '/config')
    ->append([
        __DIR__ . '/.php-cs-fixer.dist.php',
        __DIR__ . '/.twig_cs.dist',
    ])
;
$config = PhpCsFixerConfig\Factory::fromRuleSet(new PhpCsFixerConfig\RuleSet\Php80());
$config
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/php-cs-fixer/.php-cs-fixer.cache')
;

return $config;
