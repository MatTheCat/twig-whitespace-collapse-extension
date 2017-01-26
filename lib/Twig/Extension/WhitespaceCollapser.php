<?php

namespace MatTheCat\Twig\Extension;

use MatTheCat\Twig;

class WhitespaceCollapser extends \Twig_Extension
{
    /**
     * @var string[]|bool
     */
    private $default;

    /**
     * @param string[]|bool $default
     */
    public function __construct($default = array('html', 'xml', 'svg'))
    {
        $this->default = $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(new Twig\TokenParser\WhitespaceCollapse());
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return array(new Twig\NodeVisitor\WhitespaceCollapser());
    }

    /**
     * @return string[]|bool
     */
    public function getDefault()
    {
        return $this->default;
    }
}
