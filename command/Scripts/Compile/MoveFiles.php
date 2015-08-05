<?php
namespace Scripts\Compile;

use Scripts\Helper\CliCommands;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * MoveFiles
 */
class MoveFiles implements ScriptInterface
{
    protected $foldersToMove = [
        '/dist/material.min.css' => '/css/material.min.css',
        '/dist/material.min.js' => '/js/material.min.js',
        '/dist/assets/' => '/assets/'
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
            CliCommands::run($command);
        }

        return true;
    }

    private function prepareCommands($basePath)
    {
        $commandTemplate = '([ -d %s ] || [ -f %s ]) && cp -R %s %s';
        $commands = [];
        foreach ($this->foldersToMove as $from => $to) {

            $locationFrom = $basePath.'/external/material-design-lite'.$from;
            $locationTo = $basePath.'/dist'.$to;

            $this->attemptCreateFolder($locationTo);

            $command = sprintf(
                $commandTemplate,
                escapeshellarg($locationFrom),
                escapeshellarg($locationFrom),
                escapeshellarg($locationFrom),
                escapeshellarg($locationTo)
            );
            $commands[] = $command;
        }

        return $commands;
    }

    private function attemptCreateFolder($location)
    {
        if (substr($location, -1) != '/'){
            $location = dirname($location);
        }

        if (!is_dir($location)){
            mkdir($location, 0777, true);
        }
    }

}
