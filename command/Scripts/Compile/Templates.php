<?php
namespace Scripts\Compile;

use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class Templates
 */
class Templates implements ScriptInterface
{
    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Compile the Twig templates';
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
        $templatesPath = $this->getTemplateFolder($basePath);
        $twigLoader = new Twig_Loader_Filesystem($templatesPath);
        $twigParameters = $this->getTemplatesParameters($basePath);

        $twig = new Twig_Environment($twigLoader);

        $templateFiles = $this->getAllFiles($basePath);
        foreach ($templateFiles as $file) {
            $fileDestination = $this->getFileDestination($templatesPath.$file);
            $folderDestination = dirname($fileDestination);
            if (!is_dir($folderDestination)) {
                if (!@mkdir($folderDestination, 0777, true)) {
                    throw new \Exception(
                        sprintf(
                            'Unable to create this directory (%s).',
                            $folderDestination
                        )
                    );
                }
            }

            $fileRendered = $twig->render($file, $twigParameters);
            if (!file_put_contents($fileDestination, $fileRendered)) {
                throw new \Exception(
                    sprintf('Unable to write this file (%s).', $fileDestination)
                );
            }
        }

        return true;
    }

    private function getTemplatesParameters($basePath)
    {
        $parser = new Yaml();
        $fileParameters = $basePath.'/config/twig.variables.yml';
        $parameters = $parser->parse(file_get_contents($fileParameters));

        return $parameters;
    }

    private function getAllFiles($basePath)
    {
        $filesRaw = shell_exec(
            sprintf(
                'find %s -maxdepth 1 -type f -name "*.twig"',
                $this->getTemplateFolder($basePath)
            )
        );
        $files = explode("\n", trim($filesRaw));

        foreach ($files as $i => $file) {
            $files[$i] = str_replace(
                $this->getTemplateFolder($basePath),
                '',
                $file
            );
        }

        return $files;
    }

    private function getFileDestination($file)
    {
        $file = str_replace('/src/views/', '/dist/html/', $file);
        $file = str_replace('.twig', '.html', $file);

        return $file;
    }

    private function getTemplateFolder($basePath)
    {
        return $basePath.'/src/views/';
    }

}
