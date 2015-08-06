<?php
namespace Scripts\Components;

use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Mdl
 */
class Mdl implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Concatenate our MDL\'s CSS to the officials';
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
        $concatComponents = $this->concatComponents($basePath);
        if (!$concatComponents) {
            throw new \Exception('Unable to concat the MDL components.');
        }

        return true;
    }

    private function concatComponents($basePath)
    {
        $mdlOfficialDir = $this->getOfficialMdlFolder($basePath);
        $components = glob($this->getOurMdlFolder($basePath).'*');
        foreach ($components as $componentFolder) {
            if (!is_dir($componentFolder)) {
                continue;
            }

            $componentName = $this->getComponentName($componentFolder);
            $componentOfficialDir = $mdlOfficialDir.$componentName;
            if (!is_dir($componentOfficialDir)) {
                throw new \Exception(
                    sprintf(
                        'Unable to find this MDL component in the real repository (%s).',
                        $componentName
                    )
                );
            }

            $concatCss = $this->concatComponent(
                $componentOfficialDir,
                $componentFolder
            );
            if (!$concatCss) {
                throw new \Exception(
                    sprintf(
                        'Unable to concat this component (%s).',
                        $componentName
                    )
                );
            }
        }

        return true;
    }

    private function concatComponent($officialFolder, $customFolder)
    {
        $componentName = $this->getComponentName($officialFolder);
        $officialSassFile = $officialFolder.'/_'.$componentName.'.scss';
        $customSassFile = $customFolder.'/_'.$componentName.'.scss';

        if (!file_exists($officialSassFile)) {
            throw new \Exception(
                sprintf('This file does not exists (%s).', $officialSassFile)
            );
        }

        if (!file_exists($customSassFile)) {
            throw new \Exception(
                sprintf('This file does not exists (%s).', $customSassFile)
            );
        }

        return file_put_contents(
            $officialSassFile,
            file_get_contents($customSassFile),
            FILE_APPEND
        );
    }

    private function getOfficialMdlFolder($basePath)
    {
        return $basePath.'/external/material-design-lite/src/';
    }

    private function getOurMdlFolder($basePath)
    {
        return $basePath.'/src/components/mdl/';
    }

    private function getComponentName($path)
    {
        return preg_replace(
            '#^.+\/([^\/]+)\/?$#',
            '$1',
            $path
        );
    }

}