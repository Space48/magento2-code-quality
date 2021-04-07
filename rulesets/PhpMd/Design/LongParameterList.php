<?php

namespace Space48\CodeQuality\RuleSets\PhpMd\Design;

use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Extends LongParameterList class with different threshold for constructor.
 */
class LongParameterList extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * Checks the number of arguments for the given function or method node against a configured threshold.
     *
     * @param \PHPMD\AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        $threshold = $this->getIntProperty('minimum');

        if ($node->getName() === '__construct') {
            $threshold = $this->getIntProperty('constructor_minimum');
        }

        $count = $node->getParameterCount();

        if ($count > $threshold) {
            $this->addViolation(
                $node,
                [
                    $node->getType(),
                    $node->getName(),
                    $count,
                    $threshold,
                ]
            );
        }
    }
}
