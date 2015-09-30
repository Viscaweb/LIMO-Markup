<?php
namespace Scripts\Mdl;

use Scripts\Exceptions\GulpCouldNotCompileException;
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
        /*
         * Run gulp and redirect the output to null, and the errors as output
         */
        $queryTemplate = 'cd %s && gulp ' . CliCommands::ARG_ONLY_ERRORS;

        $query = sprintf(
            $queryTemplate,
            escapeshellarg($this->getMdlFolder($basePath))
        );

        $outputErrors = CliCommands::run($query);
        if (!empty($outputErrors)){
            throw new GulpCouldNotCompileException($outputErrors);
        }

        return true;
    }

    private function getMdlFolder($basePath)
    {
        return $basePath.'/external/material-design-lite/';
    }

}
