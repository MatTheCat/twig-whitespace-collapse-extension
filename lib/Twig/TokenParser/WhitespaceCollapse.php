<?php

namespace MatTheCat\Twig\TokenParser;

use MatTheCat\Twig\Node\WhitespaceCollapse as WhitespaceCollapseNode;

class WhitespaceCollapse extends \Twig_TokenParser
{
    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        if ($stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            $value = true;
        } else {
            $expr = $this->parser->getExpressionParser()->parseExpression();
            if (!$expr instanceof \Twig_Node_Expression_Constant) {
                throw new \Twig_Error_Syntax('State must be a Boolean.', $stream->getCurrent()->getLine(), $stream->getFilename());
            }
            $value = $expr->getAttribute('value');
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new WhitespaceCollapseNode($value, $body, $lineno, $this->getTag());
    }

    /**
     * @param \Twig_Token $token
     *
     * @return bool
     */
    public function decideBlockEnd(\Twig_Token $token)
    {
        return $token->test('endwhitespacecollapse');
    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'whitespacecollapse';
    }
}
