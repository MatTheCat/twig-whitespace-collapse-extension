<?php

namespace MatTheCat\Twig\Extension;

use MatTheCat\Twig;

class WhitespaceCollapser extends \Twig_Extension
{
    /**
     * @var
     */
    private $default;

    /**
     * @param array|bool $default
     */
    public function __construct($default = array('html', 'xml', 'svg'))
    {
        $this->default = $default;
    }

    public function getTokenParsers()
    {
        return array(new Twig\TokenParser\WhitespaceCollapse());
    }

    public function getNodeVisitors()
    {
        return array(new Twig\NodeVisitor\WhitespaceCollapser());
    }

    public function getDefault()
    {
        return $this->default;
    }
}
