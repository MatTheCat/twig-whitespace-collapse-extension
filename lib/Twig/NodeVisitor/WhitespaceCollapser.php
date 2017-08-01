<?php

namespace MatTheCat\Twig\NodeVisitor;

use MatTheCat\Twig\Extension\WhitespaceCollapser as WhitespaceCollapseExtension;
use MatTheCat\Twig\Node\WhitespaceCollapse as WhitespaceCollapseNode;

class WhitespaceCollapser extends \Twig_BaseNodeVisitor
{
    protected $blocks = array();
    protected $enabledByDefault = false;
    protected $previousNode;
    protected $statusStack = array();

    protected function doEnterNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Block) {
            $this->statusStack[] = isset($this->blocks[$node->getAttribute('name')]) ?
                $this->blocks[$node->getAttribute('name')] :
                $this->needCollapsing();
        } elseif ($node instanceof WhitespaceCollapseNode) {
            $this->statusStack[] = $node->getAttribute('value');
        } elseif ($env->hasExtension(WhitespaceCollapseExtension::class)) {
            /** @var \MatTheCat\Twig\Extension\WhitespaceCollapser $extension */
            $extension = $env->getExtension(WhitespaceCollapseExtension::class);
            $extensionDefault = $extension->getDefault();

            if ($node instanceof \Twig_Node_Module) {
                if (is_array($extensionDefault)) {
                    $filename = $node->getTemplateName();
                    if (substr($filename, -5) === '.twig') {
                        $filename = substr($filename, 0, -5);
                    }
                    $this->enabledByDefault = in_array(pathinfo($filename, PATHINFO_EXTENSION), $extensionDefault, true);
                } else {
                    $this->enabledByDefault = $extensionDefault;
                }
            } elseif ($node instanceof \Twig_Node_AutoEscape) {
                $this->statusStack[] = is_array($extensionDefault) ?
                    in_array($node->getAttribute('value'), $extensionDefault) :
                    $extensionDefault;
            }
        }

        return $node;
    }

    protected function doLeaveNode(\Twig_Node $node, \Twig_Environment $env)
    {
        if ($node instanceof WhitespaceCollapseNode || $node instanceof \Twig_Node_Block || $node instanceof \Twig_Node_AutoEscape) {
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
                    array('/\s</', '/>\s/', '/<\s/', '/\s>/'),
                    array('<', '>', '<', '>'),
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
