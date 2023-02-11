<?php

declare(strict_types=1);

use AdamWojs\PhpCsFixerPhpdocForceFQCN\Fixer\Phpdoc\ForceFQCNFixer;
use Ibexa\CodeStyle\PhpCsFixer\Config;

$config = new Config();
$config->registerCustomFixers([
    new ForceFQCNFixer(),
]);

$specificRules = [
    'AdamWojs/phpdoc_force_fqcn_fixer' => true,
];
$config->setRules(array_merge(
    $config->getRules(),
    $specificRules,
));

return $config
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(
                array_filter([
                    __DIR__ . '/src',
                    __DIR__ . '/tests',
                ], 'is_dir')
            )
            ->files()->name('*.php')
    );