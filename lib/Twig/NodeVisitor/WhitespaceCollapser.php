<?php

namespace MatTheCat\Twig\NodeVisitor;

use MatTheCat\Twig\Node\WhitespaceCollapse;

class WhitespaceCollapser extends \Twig_BaseNodeVisitor
{
    protected $blocks = array();
    protected $enabledByDefault = false;
    protected $previousNode;
    protected $statusStack = array();

    protected function doEnterNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if (
            $node instanceof \Twig_Node_Module &&
            $env->hasExtension('whitespace_collapser')
        ) {
            /** @var \MatTheCat\Twig\Extension\WhitespaceCollapser $extension */
            $extension = $env->getExtension('whitespace_collapser');
            $default = $extension->getDefault();
            if (is_array($default)) {
                $filename = $node->getAttribute('filename');
                if (substr($filename, -5) === '.twig') {
                    $filename = substr($filename, 0, -5);
                }
                $this->enabledByDefault = in_array(pathinfo($filename, PATHINFO_EXTENSION), $default, true);
            } else {
                $this->enabledByDefault = $default;
            }
        } elseif ($node instanceof \Twig_Node_Block) {
            $this->statusStack[] = isset($this->blocks[$node->getAttribute('name')]) ?
                $this->blocks[$node->getAttribute('name')] :
                $this->needCollapsing();
        } elseif ($node instanceof WhitespaceCollapse) {
            $this->statusStack[] = $node->getAttribute('value');
        }

        return $node;
    }

    protected function doLeaveNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if ($node instanceof WhitespaceCollapse || $node instanceof \Twig_Node_Block) {
            array_pop($this->statusStack);
        } elseif ($node instanceof \Twig_Node_BlockReference) {
            $this->blocks[$node->getAttribute('name')] = $this->needCollapsing();
        } elseif ($node instanceof \Twig_Node_Text && $this->needCollapsing()) {
            $text = $node->getAttribute('data');
            if (
                $this->previousNode instanceof \Twig_Node_Text &&
                ctype_space(substr($this->previousNode->getAttribute('data'), -1))
            ) {
                $text = ltrim($text);
            }
            if ($text === '') {
                return false;
            }

            $node->setAttribute(
                'data',
                preg_replace(
                    array('/\s{2,}/', '/<\s/', '/\s>/'),
                    array(' ', '<', '>'),
                    $text
                )
            );
        }

        $this->previousNode = $node;

        return $node;
    }

    protected function needCollapsing()
    {
        return empty($this->statusStack) ? $this->enabledByDefault : end($this->statusStack);
    }

    public function getPriority()
    {
        return 0;
    }
}
