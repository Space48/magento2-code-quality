<?php

namespace Space48\CodeQuality\Command;

use GrumPHP\Configuration\Model\HooksConfig;
use GrumPHP\IO\ConsoleIO;
use GrumPHP\Process\ProcessBuilder;
use GrumPHP\Util\Filesystem;
use GrumPHP\Util\Paths;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Adds '--fix' command line option to the 'run' command
 */
class RunCommand extends \GrumPHP\Console\Command\RunCommand
{

    /**
     * {@inheritdoc }
     */
    protected function configure(): void
    {
        parent::configure();

        $this->addOption(
            'fix',
            'f',
            InputOption::VALUE_OPTIONAL,
            'autofix at the end',
            'empty'
        );
    }

    /**
     * {@inheritdoc }
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $autofix = $input->getOption('fix');

        if ($autofix !== 'empty') {
            // covers variants:
            // 'run --fix' - fix_by_default=true
            // 'run --fix 1' - fix_by_default=true
            // 'run --fix 0' - fix_by_default=false
            // 'run' - fix_by_default=false
            $_ENV['config_override']['fix_by_default'] = $autofix === null ? true : (bool) $autofix;
        }

        return parent::execute($input, $output);
    }
}
