<?php

namespace Space48\CodeQuality\Task;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PhpMd task.
 */
class PhpMd extends \GrumPHP\Task\PhpMd
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = parent::getConfigurableOptions();
        $resolver->addAllowedValues('report_format', ['xml']);

        return $resolver;
    }

}
