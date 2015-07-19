<?php
namespace Scripts\Compile;

use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * MoveFiles
 */
class MoveFiles implements ScriptInterface
{
    protected $foldersToMove = [
        '/dist/material.min.css' => '/css/material.min.css'
    ];

    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Move the compiled files';
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
        $commands = $this->prepareCommands($basePath);

        foreach ($commands as $command) {
            shell_exec($command);
        }

        return true;
    }

    private function prepareCommands($basePath)
    {
        $commandTemplate = 'cp -R %s %s && true';
        $commands = [];
        foreach ($this->foldersToMove as $from => $to) {
            $command = sprintf(
                $commandTemplate,
                $basePath.'/external/material-design-lite'.$from,
                $basePath.'/dist'.$to
            );
            $commands[] = $command;
        }

        return $commands;
    }

}
