<?php
namespace Scripts\Components;

use Scripts\Helper\CliCommands;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Assets
 */
class Assets implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Copy the Livescore assets the MDL project';
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
        $copyComponents = $this->copyAssets($basePath);
        if (!$copyComponents) {
            throw new \Exception('Unable to copy the Livescore assets.');
        }

        return true;
    }

    private function copyAssets($basePath)
    {
        $folderLivescoreAssets = $this->getLivescoreAssetsFolder(
            $basePath
        );
        $folderMdlAssets = $this->getMdlFolder($basePath);

        if (!is_dir($folderMdlAssets)){
            CliCommands::run('mkdir -p '.$folderMdlAssets);
        }

        $queryTemplate = 'cp -R %s %s';
        $query = sprintf(
            $queryTemplate,
            escapeshellarg($folderLivescoreAssets).'*',
            escapeshellarg($folderMdlAssets)
        );

        CliCommands::run($query);

        return true;
    }

    private function getMdlFolder($basePath)
    {
        return $basePath.'/external/material-design-lite/docs/_assets/';
    }

    private function getLivescoreAssetsFolder($basePath)
    {
        return $basePath.'/src/assets/';
    }

}
