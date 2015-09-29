<?php
namespace Scripts\Helper;

/**
 * Class CliCommands
 */
class CliCommands
{
    const COMMAND_INFINITE_LOOP = "while true\ndo\n%s\ndone";
    const COMMAND_FSWATCH = 'fswatch-run %s %s %s';
    const COMMAND_INOTIFY = 'inotifywait -qq -r -e modify -e move -e create -e delete %s && %s';
    const COMMAND_RUN_TASK = 'cd %s && php application.php %s %s %s';
    const COMMAND_MDL_GULP_TASK = 'cd %s && gulp %s';
    const COMMAND_COMMON = '%s %s';

    const ARG_AS_BG = '> /dev/null 2>&1 &';
    const ARG_ONLY_ERRORS = '2>&1 >/dev/null';

    /**
     * @param $command
     *
     * @return string
     */
    static public function run($command)
    {
        return shell_exec($command);
    }

    /**
     * @param string $path          Folder to watch
     * @param string $script        Script to run
     * @param bool   $asBackground  Run as background
     * @param bool   $returnCommand Return the command string
     *
     * @return string
     */
    static public function runFsWatch(
        $path,
        $script,
        $asBackground = false,
        $returnCommand = false
    ) {
        if (System::isUbuntu()) {
            $commandInotify = sprintf(
                self::COMMAND_INOTIFY,
                escapeshellarg($path),
                escapeshellarg($script)
            );
            $commandInotifyInfinite = sprintf(
                self::COMMAND_INFINITE_LOOP,
                $commandInotify
            );
            $commandInotifyInfiniteFile = self::createExecutableFileFromCommand(
                $commandInotifyInfinite
            );
            $command = sprintf(
                self::COMMAND_COMMON,
                $commandInotifyInfiniteFile,
                $asBackground ? self::ARG_AS_BG : ''
            );
        } else {
            $command = sprintf(
                self::COMMAND_FSWATCH,
                escapeshellarg($path),
                escapeshellarg($script),
                $asBackground ? self::ARG_AS_BG : ''
            );
        }
        if ($returnCommand) {
            return $command;
        }

        return self::run($command);
    }

    /**
     * @param string $taskName      Task name
     * @param bool   $asBackground  Run as background
     * @param bool   $returnCommand Return the command string
     *
     * @return string
     */
    static public function runTask(
        $taskName,
        $asBackground = false,
        $returnCommand = false
    ) {
        $command = sprintf(
            self::COMMAND_RUN_TASK,
            escapeshellarg(realpath(__DIR__.'/../../../')),
            escapeshellarg('run-any'),
            escapeshellarg($taskName),
            $asBackground ? self::ARG_AS_BG : ''
        );
        if ($returnCommand) {
            return $command;
        }

        return self::run($command);
    }

    /**
     * @param string $path         Folder to watch
     * @param string $taskName     Task name
     * @param bool   $asBackground Run as background
     *
     * @return string
     */
    static public function runTaskUsingFsWatch(
        $path,
        $taskName,
        $asBackground = false
    ) {
        $command = self::runTask($taskName, false, true);
        $bashTmpFile = self::createExecutableFileFromCommand($command);

        return self::runFsWatch($path, $bashTmpFile, $asBackground);
    }

    /**
     * @param string    $taskName Task name
     * @param bool|true $throwErrors
     *
     * @return string
     */
    static public function runTaskMdlGulp(
        $taskName,
        $throwErrors = false
    ) {
        $mdlFolder = realpath(
            __DIR__.'/../../../external/material-design-lite'
        );

        $commandTemplate = self::COMMAND_MDL_GULP_TASK;
        if ($throwErrors) {
            $commandTemplate .= ' '.self::ARG_ONLY_ERRORS;
        }

        $command = sprintf(
            $commandTemplate,
            escapeshellarg($mdlFolder),
            $taskName
        );

        return self::run($command);
    }

    /**
     * @param $command
     *
     * @return string
     */
    static private function createExecutableFileFromCommand($command)
    {
        $bashScript = '#!/bin/bash'."\n".$command;
        $bashTmpFile = sys_get_temp_dir().'/limo_cli_task_'.md5(
                $bashScript
            ).'.sh';
        file_put_contents($bashTmpFile, $bashScript);
        self::run(sprintf('chmod +x %s', escapeshellarg($bashTmpFile)));

        return $bashTmpFile;
    }

}
