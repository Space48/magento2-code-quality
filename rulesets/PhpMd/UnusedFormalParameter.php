<?php

namespace Space48\CodeQuality\RuleSets\PhpMd;

use Space48\CodeQuality\Utils\MagentoClassTypeResolver;
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
     * This method checks that all parameters of a given function or method are
     * used at least one time within the artifacts body.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        if (MagentoClassTypeResolver::isPlugin($node) && MagentoClassTypeResolver::isPluginMethod($node)) {
            return;
        }

        parent::apply($node);
    }
}
