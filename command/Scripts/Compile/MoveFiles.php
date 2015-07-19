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
        '/dist/material.min.css' => '/css/material.min.css',
        '/dist/assets/' => '/css/assets/'
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
        $commandTemplate = '[ -d "%s" ] && cp -R %s %s';
        $commands = [];
        foreach ($this->foldersToMove as $from => $to) {

            $locationFrom = $basePath.'/external/material-design-lite'.$from;
            $locationTo = $basePath.'/dist'.$to;

            $this->attemptCreateFolder($locationTo);

            $command = sprintf(
                $commandTemplate,
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
