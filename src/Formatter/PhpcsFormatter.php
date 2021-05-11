<?php

namespace Space48\CodeQuality\Formatter;

use Symfony\Component\Process\Process;

class PhpcsFormatter extends \GrumPHP\Formatter\PhpcsFormatter
{

    public function format(Process $process): string
    {
        $output = sprintf("\n%'-90s\n", '-');
        $output .= 'PHPCS. (To exclude: "//phpcs:disable Rule.Name". Disables rule to the end of the file)' . "\n";
        $output .= sprintf("%'-90s\n\n", '-');

        return $output . parent::format($process);
    }
}
