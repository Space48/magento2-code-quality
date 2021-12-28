<?php

namespace Space48\CodeQuality\RuleSets\PhpMd\Magento;


use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;
use Space48\CodeQuality\Utils\MagentoClassType;

class EntryPointClassComplexity extends AbstractRule implements FunctionAware, MethodAware
{
    /**
     * @param AbstractNode $node
     */
    public function apply(AbstractNode $node)
    {
        if (!MagentoClassType::isPlugin($node) && !MagentoClassType::isObserver($node)) {
            return;
        }

        $methodName = $node->getName();
        if ($methodName === '__construct') {
            return;
        }

        if (MagentoClassType::isPluginMethod($node) || MagentoClassType::isObserverMethod($node)) {
            $this->applyForMethod($node, 'reportLevel', 'entry point');
        } else {
            $this->applyForMethod($node, 'reportLevelPrivate', 'private');
        }
    }

    /**
     * @param AbstractNode $node
     * @param string $thresholdProperty
     * @param string $methodType
     */
    private function applyForMethod(AbstractNode $node, string $thresholdProperty, string $methodType)
    {
        $threshold = $this->getIntProperty($thresholdProperty);
        $ccn = $node->getMetric('ccn2');

        if ($ccn > $threshold) {
            $this->addViolation(
                $node,
                array(
                    $node->getType(),
                    $node->getName(),
                    $ccn,
                    $threshold,
                    $methodType
                )
            );
        }
    }
}
