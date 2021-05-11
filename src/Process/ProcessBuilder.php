<?php

namespace Space48\CodeQuality\Process;

use GrumPHP\Collection\ProcessArgumentsCollection;
use GrumPHP\Exception\PlatformException;
use Symfony\Component\Process\Process;

class ProcessBuilder extends \GrumPHP\Process\ProcessBuilder
{

    /**
     * @throws PlatformException
     */
    public function buildProcess(ProcessArgumentsCollection $arguments): Process
    {
        // A workaround to add '-s' flag to PhpCs linter that prints rule name next to violation.
        // Alternative is to completely replace PhpCs Task as runtime arguments are hardcoded there.
        if (array_filter($arguments->toArray(), function ($argument) { return preg_match('/\/phpcs$/', $argument); })) {
            $arguments->add('-s');
        }

        return parent::buildProcess($arguments);
    }

}
