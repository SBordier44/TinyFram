<?php
$config = PhpCsFixer\Config::create()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(__DIR__)
    );
return $config;
