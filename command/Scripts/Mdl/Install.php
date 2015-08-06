<?php
namespace Scripts\Mdl;

use Scripts\Helper\CliCommands;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Install
 */
class Install implements ScriptInterface
{
    const GIT_PROJECT_URL = 'https://github.com/google/material-design-lite.git';

    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Initialize the official Google MDL Repository';
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
        $gitInitialized = $this->isGitInitialized($gitRepository);
        if (!$gitInitialized) {
            if ($this->initializeGitProject($gitRepository)) {
                $gitInitialized = true;
            } else {
                throw new \Exception('Unable to initialize the GIT project');
            }
        }

        $gulpInitialized = $this->initializeGulp($gitRepository);

        return ($gitInitialized && $gulpInitialized);
    }

    private function initializeGulp($folder)
    {
        CliCommands::run(
            sprintf(
                'cd %s && npm --silent install && npm --silent install -g gulp  > /dev/null 2>&1',
                escapeshellarg($folder)
            )
        );

        return true;
    }

    private function initializeGitProject($folder)
    {
        if (!is_dir($folder)) {
            if (!mkdir($folder, 0777, true)) {
                return false;
            }
        }

        CliCommands::run(
            sprintf(
                'cd %s && git clone %s . > /dev/null 2>&1',
                escapeshellarg($folder),
                escapeshellarg(self::GIT_PROJECT_URL)
            )
        );

        return $this->isGitInitialized($folder);
    }

    private function isGitInitialized($folder)
    {
        return file_exists($folder.'.git/HEAD');
    }

}
