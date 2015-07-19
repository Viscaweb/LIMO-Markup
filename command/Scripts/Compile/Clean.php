<?php
namespace Scripts\Compile;

use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Clean
 */
class Clean implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Clean the compiled folder';
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
        $commandTemplate = 'rm -rf %s';
        $folders = [
            $basePath.'/dist/css/*',
            $basePath.'/dist/fonts/*',
//            $basePath.'/dist/html/*',
            $basePath.'/dist/images/*',
        ];
        foreach($folders as $folder){
            $command = sprintf($commandTemplate, $folder);
            shell_exec($command);
        }

        return true;
    }


}
