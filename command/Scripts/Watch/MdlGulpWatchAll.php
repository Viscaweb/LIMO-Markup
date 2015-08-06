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
    const DETECT_TASK_REGEX = '#gulp\.task\(\'serve\'\,.+[\n]\}\)\;#isU';
    const DETECT_CONNECT_SERVER_REGEX = '#\$\.connect.+\}\)\;#isU';
    const DETECT_DIST_OPEN_REGEX = '#gulp\.src.+open.+\}\)\)\;#isU';

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

        if (strstr($gulpContent, 'watchall')){
            return true;
        }

        if (!preg_match(self::DETECT_TASK_REGEX, $gulpContent, $args)) {
            throw new \Exception(
                'Unable to find the task "serve" in the MDL\'s Gulp file.'
            );
        }

        $defaultTaskContent = $args[0];

        $watchAllTask = preg_replace(
            self::DETECT_CONNECT_SERVER_REGEX,
            '',
            $defaultTaskContent
        );
        $watchAllTask = preg_replace(
            self::DETECT_DIST_OPEN_REGEX,
            '',
            $watchAllTask
        );

        $watchAllTask = str_replace("'serve'", "'watchall'", $watchAllTask);
        $watchAllTask = str_replace(", ['default']", '', $watchAllTask);
        $watchAllTask = preg_replace("#\n{2,}#", "\n", $watchAllTask);

        $newFileContent = str_replace(
            $defaultTaskContent,
            $defaultTaskContent.$watchAllTask,
            $gulpContent
        );

        $newFileContent = str_replace(
                "gulp.task('serve'",
            "gulp.task('watchall', function() {\n  watch();\n});\n"."gulp.task('serve'",
                $gulpContent);

        return file_put_contents($gulpFile, $newFileContent);
    }

}
