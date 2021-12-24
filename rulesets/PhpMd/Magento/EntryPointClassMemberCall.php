<?php

namespace Space48\CodeQuality\RuleSets\PhpMd\Magento;

use PHPMD\AbstractNode;
use Space48\CodeQuality\Utils\MagentoClassType;

class EntryPointClassMemberCall extends \PHPMD\AbstractRule implements \PHPMD\Rule\ClassAware
{
    /**
     * @param AbstractNode $node
     */
    public function apply(AbstractNode $node)
    {
        if (!MagentoClassType::isPlugin($node) && !MagentoClassType::isObserver($node)) {
            return;
        }

        $allowedCallsNumber = $this->getIntProperty('max-method-calls');
        $calls = [];

        $members = $node->findChildrenOfType('MemberPrimaryPrefix');
        foreach ($members as $member) {
            $owner = $member->getChild(0);
            if (!$owner || $owner->getImage() !== '$this') {
                continue;
            }

            $target = $member->getChild(1);
            if ($target && $target->getNode() instanceof \PDepend\Source\AST\ASTMethodPostfix) {
                $calls[] = $target->getNode()->getImage();
            }
        }

        $calls = array_unique($calls);
        if (count($calls) > $allowedCallsNumber) {
            $this->addViolation(
                $node,
                [
                    count($calls),
                    implode(', ', array_map(function ($function) { return "'{$function}'"; }, $calls)),
                    $allowedCallsNumber
                ]
            );
        }
    }
}
