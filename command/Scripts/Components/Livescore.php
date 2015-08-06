<?php
namespace Scripts\Components;

use Scripts\Helper\CliCommands;
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
        $copyComponents = $this->copyComponents($basePath);
        if (!$copyComponents) {
            throw new \Exception('Unable to copy the Livescore components.');
        }

        $addComponentsInCss = $this->addComponentsInMainCss($basePath);
        if (!$addComponentsInCss) {
            throw new \Exception(
                'Unable to add Livescore components in the main MDL\'s CSS.'
            );
        }

        return true;
    }

    private function addComponentsInMainCss($basePath)
    {
        $folders = glob($this->getLivescoreComponentsFolder($basePath).'*');
        $cssToAdd = "\n// Livescore Components";
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                continue;
            }

            $folderName = preg_replace('#^.+\/([^\/]+)\/?$#', '$1', $folder);

            $cssToAdd .= "\n";
            $cssToAdd .= sprintf('@import "%s/%s";', $folderName, $folderName);
        }

        $cssFile = $this->getMdlMainCss($basePath);

        return file_put_contents($cssFile, $cssToAdd, FILE_APPEND);
    }

    private function copyComponents($basePath)
    {
        $folderLivescoreComponents = $this->getLivescoreComponentsFolder(
            $basePath
        );
        $folderMdlComponents = $this->getMdlFolder($basePath);

        $queryTemplate = 'cp -R %s %s';
        $query = sprintf(
            $queryTemplate,
            escapeshellarg($folderLivescoreComponents).'*',
            escapeshellarg($folderMdlComponents)
        );

        CliCommands::run($query);

        return true;
    }

    private function getMdlFolder($basePath)
    {
        return $basePath.'/external/material-design-lite/src/';
    }

    private function getLivescoreComponentsFolder($basePath)
    {
        return $basePath.'/src/components/livescore/';
    }

    private function getMdlMainCss($basePath)
    {
        return $this->getMdlFolder(
            $basePath
        ).'material-design-lite.scss';
    }
}
