<?php

namespace Space48\CodeQuality\Task;

use GrumPHP\Task\Config\ConfigOptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PhpMd task.
 */
class PhpMd extends \GrumPHP\Task\PhpMd
{
    public static function getConfigurableOptions(): ConfigOptionsResolver
    {
        // due to Factory pattern they used we can no longer extend Options after calling parent method
        // copy from parent, changes are marked with "@added-by-Space48"
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'whitelist_patterns' => [],
            'exclude' => [],
            'report_format' => 'text',
            'ruleset' => ['cleancode', 'codesize', 'naming'],
            'triggered_by' => ['php'],
        ]);

        $resolver->addAllowedTypes('whitelist_patterns', ['array']);
        $resolver->addAllowedTypes('exclude', ['array']);
        $resolver->addAllowedTypes('report_format', ['string']);
        $resolver->addAllowedValues('report_format', ['text', 'ansi', 'xml']); // @added-by-Space48: "xml" format
        $resolver->addAllowedTypes('ruleset', ['array']);
        $resolver->addAllowedTypes('triggered_by', ['array']);

        return ConfigOptionsResolver::fromOptionsResolver($resolver);
    }
}
