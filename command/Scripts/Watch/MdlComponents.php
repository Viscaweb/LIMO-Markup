<?php
namespace Scripts\Watch;

use Scripts\Helper\CliCommands;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MdlComponents
 */
class MdlComponents implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Watch the custom MDL components';
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
        $folderToWatch = $basePath.'/src/components/mdl';

        CliCommands::runTaskUsingFsWatch(
            $folderToWatch,
            'Components\Mdl',
            true
        );

        return true;
    }


}
