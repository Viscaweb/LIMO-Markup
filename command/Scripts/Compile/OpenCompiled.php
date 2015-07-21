<?php
namespace Scripts\Compile;

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
        $distFolder = $basePath.'/dist';

        $commandTemplate = 'open %s';
        $command = sprintf($commandTemplate, $distFolder);

        shell_exec($command);

        return true;
    }


}
