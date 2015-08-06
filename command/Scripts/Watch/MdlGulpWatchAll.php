<?php
namespace Scripts\Watch;

use Scripts\Helper\CliCommands;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MdlGulpWatchAll
 */
class MdlGulpWatchAll implements ScriptInterface
{

    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Watch the MDL\'s components';
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
        $this->createWatchAllTask($basePath);

        CliCommands::runTaskMdlGulp('watchall');

        return true;
    }

    /**
     * @param string $basePath Base Path
     *
     * @return bool|int
     * @throws \Exception
     */
    private function createWatchAllTask($basePath)
    {
        $gulpFile = $basePath.'/external/material-design-lite/gulpfile.js';
        $gulpContent = file_get_contents($gulpFile);

        if (strstr($gulpContent, 'watchall')) {
            return true;
        }

        $newFileContent = str_replace(
            self::TASK_SERVE,
            self::TASK_WATCHALL."\n".self::TASK_SERVE,
            $gulpContent
        );

        return file_put_contents($gulpFile, $newFileContent);
    }

    const TASK_SERVE = "gulp.task('serve'";

    const TASK_WATCHALL = "gulp.task('watchall', function() {\n  watch();\n});";

}
