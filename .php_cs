<?php

$finder = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(array('lib'));

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules(['@Symfony' => true]);
