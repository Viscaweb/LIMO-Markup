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
        $concatComponents = $this->concatComponents($basePath);
        if (!$concatComponents) {
            throw new \Exception('Unable to concat the MDL components.');
        }

        $concatSingleFiles = $this->concatSingleFiles($basePath);
        if (!$concatSingleFiles) {
            throw new \Exception('Unable to concat the MDL single files.');
        }

        return true;
    }

    private function concatSingleFiles($basePath)
    {
        $mdlOfficialDir = $this->getOfficialMdlFolder($basePath);
        $singleFiles = glob($this->getOurMdlFolder($basePath).'*.scss');
        foreach ($singleFiles as $file) {
            $mdlOfficialFile = $mdlOfficialDir.$this->getFileName($file);
            if (!file_exists($mdlOfficialFile)) {
                throw new \Exception(
                    sprintf(
                        'You are trying to overwrite a file (%s) that does not exists in the MDL components',
                        $file
                    )
                );
            }

            $append = (!strstr($file, '_variables.scss'));

            if (!$this->concatFromBak($file, $mdlOfficialFile, $append)) {
                return false;
            }
        }

        return true;
    }

    private function concatComponents($basePath)
    {
        $mdlOfficialDir = $this->getOfficialMdlFolder($basePath);
        $components = glob($this->getOurMdlFolder($basePath).'*', GLOB_ONLYDIR);
        foreach ($components as $componentFolder) {
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

        return $this->concatFromBak($customSassFile, $officialSassFile);
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

    private function getFileName($path)
    {
        return preg_replace(
            '#^.+\/([^\/]+)$#',
            '$1',
            $path
        );
    }

    /**
     * @param           $customFile
     * @param           $mdlOfficialFile
     * @param bool|true $append
     *
     * @return bool|int
     */
    private function concatFromBak(
        $customFile,
        $mdlOfficialFile,
        $append = true
    ) {
        $mdlOfficialFileBak = $mdlOfficialFile.'.bak';
        if (file_exists($mdlOfficialFileBak)) {
            if (!copy($mdlOfficialFileBak, $mdlOfficialFile)) {
                return false;
            }
        } else {
            if (!copy($mdlOfficialFile, $mdlOfficialFileBak)) {
                return false;
            }
        }

        if ($append) {
            $status = file_put_contents(
                $mdlOfficialFile,
                file_get_contents($customFile),
                FILE_APPEND
            );
        } else {
            $status = file_put_contents(
                $mdlOfficialFile,
                file_get_contents($customFile).
                file_get_contents($mdlOfficialFileBak)
            );
        }

        return $status;
    }

}