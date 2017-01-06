<?php

namespace MatTheCat\Twig\Tests;

use MatTheCat\Twig\Extension\WhitespaceCollapser;

class IntegrationTest extends \Twig_Test_IntegrationTestCase
{
    /**
     * @inheritDoc
     */
    protected function getFixturesDir()
    {
        return __DIR__.'/Fixtures/';
    }

    /**
     * @inheritdoc
     */
    protected function getExtensions()
    {
        return array(new WhitespaceCollapser());
    }
}