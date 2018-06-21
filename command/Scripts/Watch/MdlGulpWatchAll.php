<?php
namespace Scripts\Watch;

use Scripts\Exceptions\GulpCouldNotCompileException;
use Scripts\Helper\CliCommands;
use Scripts\Interfaces\ScriptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MdlGulpWatchAll
 */
class MdlGulpWatchAll implements ScriptInterface
{

    const MESSAGE_TEMPLATE = "Something wrong happened at %s with the message: %s";

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

        do {
            try {
                $output->writeln('Watching to compile...');
                $this->runWatchAll();
            } catch (GulpCouldNotCompileException $ex) {
                $message = sprintf(
                    self::MESSAGE_TEMPLATE,
                    date('c'),
                    $ex->getMessage()
                );
                $output->writeln($message);
                sleep(1);
            }
        } while (1);

        return true;
    }

    /**
     * This method is infinite, the command is always running unless an exception is thrown.
     *
     * @throws GulpCouldNotCompileException
     */
    private function runWatchAll()
    {
        $outputErrors = CliCommands::runTaskMdlGulp('watchall', true);
        if (!empty($outputErrors)) {
            throw new GulpCouldNotCompileException($outputErrors);
        }
    }

    /**
     * @param string $basePath Base Path
     *
     * @return bool|int
     * @throws \Exception
     */
    private function createWatchAllTask($basePath)
    {
        $gulpFile = $basePath.'/external/material-design-lite/gulpfile.babel.js';
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
