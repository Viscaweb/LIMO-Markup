<?php
namespace Scripts\Watch;

use Scripts\Helper\CliCommands;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MdlMoveFiles
 */
class MdlMoveFiles implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Move the MDL\'s files when there is an update.';
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
        $folderToWatch = $basePath.'/external/material-design-lite/dist';

        CliCommands::runTaskUsingFsWatch(
            $folderToWatch,
            'Compile\MoveFiles',
            true
        );

        return true;
    }


}
