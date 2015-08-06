<?php
namespace Scripts\Compile;

use Scripts\Exceptions\CommandNotAvailableException;
use Scripts\Helper\CliCommands;
use Scripts\Helper\System;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OpenCompiled
 */
class OpenCompiled implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Open the compiled folder';
    }

    /**
     * Execute the script
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $basePath
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output,
        $basePath
    ) {
        $distFolder = $basePath.'/dist';
        if (!(System::isMac())) {
            throw new CommandNotAvailableException(
                'open',
                sprintf('You can manually open this folder: %s', $distFolder)
            );
        }


        $commandTemplate = 'open %s';
        $command = sprintf($commandTemplate, escapeshellarg($distFolder));

        CliCommands::run($command);

        return true;
    }


}
