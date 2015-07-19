<?php
namespace Scripts\Components;

use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Livescore
 */
class Livescore implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Copy the Livescore toward the MDL project';
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
        $folderLivescoreComponents = $basePath.'/src/components/livescore/';
        $folderMdlComponents = $basePath.'/external/material-design-lite/src/';

        $queryTemplate = 'cp -R %s %s';
        $query = sprintf(
            $queryTemplate,
            escapeshellarg($folderLivescoreComponents),
            escapeshellarg($folderMdlComponents)
        );

        return shell_exec($query);
    }
}
