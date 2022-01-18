<?php

namespace Space48\CodeQuality\Configuration;

class FixerConfig extends \GrumPHP\Configuration\Model\FixerConfig
{
    /**
     * {@inheritdoc }
     */
    public static function fromArray(array $config): self
    {
        return new self(
            ($config['enabled'] ?? false),
            ($config['fix_by_default'] ?? false)
        );
    }

    /**
     * {@inheritdoc }
     */
    public function fixByDefault(): bool
    {
        return $_ENV['config_override']['fix_by_default'] ?? parent::fixByDefault();
    }
}
