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
    const MDL_VARIABLES_BEFORE = '_variables_before.scss';
    const MDL_VARIABLES_AFTER = '_variables_after.scss';

    protected $variablesBeforeAlreadyHandled = false;
    protected $variablesAfterAlreadyHandled = false;

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

            if (($variableDetectResult = $this->isVariableFile($file)) !== 0) {
                $mdlOfficialFile = $mdlOfficialDir.$this->getFileName($file);
                $mdlOfficialFile = preg_replace(
                    '#\/([^\/]+)$#',
                    '/_variables.scss',
                    $mdlOfficialFile
                );
                $append = (!strstr($file, '_variables.scss'));

                switch ($variableDetectResult) {
                    # Before
                    case -1:
                        $append = false; // prepend
                        $useBakFile = !($this->variablesAfterAlreadyHandled);
                        $this->variablesBeforeAlreadyHandled = true;
                        break;
                    # After
                    case 1:
                        $append = true;
                        $useBakFile = !($this->variablesBeforeAlreadyHandled);
                        $this->variablesAfterAlreadyHandled = true;
                        break;
                }

                if (!$this->concatFromBak(
                    $file,
                    $mdlOfficialFile,
                    $append,
                    $useBakFile
                )
                ) {
                    return false;
                }
            } else {
                $mdlOfficialFile = $mdlOfficialDir.$this->getFileName($file);
                if (!file_exists($mdlOfficialFile)) {
                    throw new \Exception(
                        sprintf(
                            'You are trying to overwrite a file (%s) that does not exists in the MDL components',
                            $file
                        )
                    );
                }
                if (!$this->concatFromBak($file, $mdlOfficialFile)) {
                    return false;
                }
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
     * @param bool|true $useBakFile
     *
     * @return bool|int
     */
    private function concatFromBak(
        $customFile,
        $mdlOfficialFile,
        $append = true,
        $useBakFile = true
    ) {
        $mdlOfficialFileBak = $mdlOfficialFile.'.bak';
        if ($useBakFile) {
            if (file_exists($mdlOfficialFileBak)) {
                if (!copy($mdlOfficialFileBak, $mdlOfficialFile)) {
                    return false;
                }
            } else {
                if (!copy($mdlOfficialFile, $mdlOfficialFileBak)) {
                    return false;
                }
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
                file_get_contents(
                    $useBakFile ? $mdlOfficialFileBak : $mdlOfficialFile
                )
            );
        }

        return $status;
    }

    /**
     * @param $filename
     *
     * @return int
     */
    private function isVariableFile($filename)
    {
        switch (true) {
            case strstr($filename, self::MDL_VARIABLES_BEFORE):
                return -1;
                break;
            case strstr($filename, self::MDL_VARIABLES_AFTER):
                return 1;
                break;
            default:
                return 0;
                break;
        }
    }

}