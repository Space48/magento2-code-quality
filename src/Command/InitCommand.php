<?php

namespace Space48\CodeQuality\Command;

use GrumPHP\Configuration\Model\HooksConfig;
use GrumPHP\Process\ProcessBuilder;
use GrumPHP\Util\Filesystem;
use GrumPHP\Util\Paths;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Removes 'commit-msg' as it will not work inside warden if there is no .git folder there
 */
class InitCommand extends \GrumPHP\Console\Command\Git\InitCommand
{
    public function __construct(
        HooksConfig $hooksConfig,
        Filesystem $filesystem,
        ProcessBuilder $processBuilder,
        Paths $paths
    ) {
        parent::__construct($hooksConfig, $filesystem, $processBuilder, $paths);

        $key = array_search('commit-msg', self::$hooks);
        if ($key !== false) {
            unset(self::$hooks[$key]);
        }
    }
}
