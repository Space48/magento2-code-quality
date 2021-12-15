<?php

namespace Space48\CodeQuality\Utils;

use PHPMD\AbstractNode;

class MagentoClassType
{
    /** @var MagentoClassTypeResolver */
    private static $resolver;

    /**
     * @param AbstractNode $node
     * @return bool
     */
    public static function isPlugin(AbstractNode $node): bool
    {
        $type = self::getResolver()->resolveType($node);

        return $type == 'plugin';
    }

    /**
     * @param AbstractNode $node
     * @return bool
     */
    public static function isObserver(AbstractNode $node): bool
    {
        $type = self::getResolver()->resolveType($node);

        return $type == 'observer';
    }

    /**
     * @param AbstractNode $node
     * @return bool
     */
    public static function isPluginMethod(AbstractNode $node): bool
    {
        return (bool)preg_match('/^(before|after|around)[A-Z].*/', $node->getName());
    }

    /**
     * @param AbstractNode $node
     * @return bool
     */
    public function isObserverMethod(AbstractNode $node): bool
    {
        return $node->getName() == 'execute';
    }

    /**
     * @return MagentoClassTypeResolver
     */
    private static function getResolver(): MagentoClassTypeResolver
    {
        if (!self::$resolver) {
            self::$resolver = new MagentoClassTypeResolver();
        }

        return self::$resolver;
    }
}
