<?php
namespace Scripts\Mdl;

use Scripts\Helper\CliCommands;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Compile
 */
class Compile implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Compile MDL using Gulp';
    }

    /**
     * Execute the script
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $basePath
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
        $queryTemplate = 'cd %s && gulp > /dev/null 2>&1';
        $query = sprintf(
            $queryTemplate,
            escapeshellarg($this->getMdlFolder($basePath))
        );

        CliCommands::run($query);

        return true;
    }

    private function getMdlFolder($basePath)
    {
        return $basePath.'/external/material-design-lite/';
    }

}
