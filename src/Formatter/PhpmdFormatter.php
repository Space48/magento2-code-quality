<?php

namespace Space48\CodeQuality\Formatter;

use Symfony\Component\Process\Process;
use Symfony\Component\DomCrawler\Crawler;

class PhpmdFormatter implements \GrumPHP\Formatter\ProcessFormatterInterface
{

    public function format(Process $process): string
    {
        $output = trim($process->getOutput());
        if (!$output) {
            return $process->getErrorOutput();
        }

        $xml = new Crawler($output);
        try {
            // will throw an exception if $output is not in expected format
            $xml->filter('file')->children();
        } catch (\Exception $e) {
            return $output;
        }

        $formattedOutput = 'PHPMD. (To exclude rule add "@SuppressWarnings(PHPMD.RuleName)" to function or class phpdoc)' . "\n";
        foreach ($xml->filter('file') as $node) {
            $name = $node->attributes->getNamedItem('name')->value;
            $formattedOutput .= sprintf("%'-". \strlen($name) . "s\n", '-');
            $formattedOutput .= $name . "\n";
            $formattedOutput .= sprintf("%'-". \strlen($name) . "s\n", '-');

            foreach ($node->childNodes as $violation) {
                if ($violation->nodeName == '#text') {
                    continue;
                }

                $formattedOutput .= sprintf(
                    "% 11s | Line: % 3s | %s | %s\n",
                    strtoupper($violation->nodeName),
                    $violation->attributes->getNamedItem('beginline')->value,
                    $violation->attributes->getNamedItem('rule')->value,
                    trim($violation->textContent)
                );
            }
        }

        return $formattedOutput;
    }
}
