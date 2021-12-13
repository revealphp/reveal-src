<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Console\Style;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

/**
 * @api
 */
final class SymfonyStyleFactory
{
    private PrivatesCaller $privatesCaller;

    public function __construct()
    {
        $this->privatesCaller = new PrivatesCaller();
    }

    public function create(): SymfonyStyle
    {
        // to prevent missing argv indexes
        if (! isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }

        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();

        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new Application(), 'configureIO', [$argvInput, $consoleOutput]);

        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        // disable output for tests
        if (StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        return new SymfonyStyle($argvInput, $consoleOutput);
    }
}