<?php
namespace Scripts\Mdl;

use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Reset
 */
class Reset implements ScriptInterface
{

    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Reset completely the official Google MDL Repository';
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
        $gitRepository = $basePath.'/external/material-design-lite/';

        return $this->resetGitRepository($gitRepository);
    }

    private function resetGitRepository($folder)
    {
        shell_exec(
            sprintf(
                'cd %s && git reset --hard > /dev/null 2>&1',
                escapeshellarg($folder)
            )
        );

        return true;
    }

}
