<?php

namespace MatTheCat\Twig\Node;

class WhitespaceCollapse extends \Twig_Node
{
    /**
     * @param bool                $value
     * @param \Twig_NodeInterface $body
     * @param int                 $lineno
     * @param string              $tag
     */
    public function __construct($value, \Twig_NodeInterface $body, $lineno, $tag = 'whitespacecollapse')
    {
        parent::__construct(array('body' => $body), array('value' => $value), $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('body'));
    }
}
