<?php

namespace Space48\CodeQuality\RuleSets\PhpMd;

use Space48\CodeQuality\Utils\MagentoClassType;
use PHPMD\AbstractNode;
use PHPMD\Node\ASTNode;
use PHPMD\Node\MethodNode;

/**
 * This rule collects all formal parameters of a given function or method that
 * are not used in a statement of the artifact's body.
 */
class UnusedFormalParameter extends \PHPMD\Rule\UnusedFormalParameter
{

    /**
     * Ignore this rule for Plugin and Observer methods.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        if (MagentoClassType::isPluginMethod($node) && MagentoClassType::isPlugin($node)) {
            return;
        }

        if (MagentoClassType::isObserverMethod($node) && MagentoClassType::isObserver($node)) {
            return;
        }

        parent::apply($node);
    }
}
