<?php
namespace Scripts\Helper;

/**
 * Class CliCommands
 */
class CliCommands
{
    const COMMAND_FSWATCH = 'fswatch-run %s %s %s';
    const COMMAND_RUN_TASK = 'cd %s && php application.php %s %s %s';
    const COMMAND_MDL_GULP_TASK = 'cd %s && gulp %s';

    const ARG_AS_BG = '> /dev/null 2>&1 &';

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
        $command = sprintf(
            self::COMMAND_FSWATCH,
            escapeshellarg($path),
            escapeshellarg($script),
            $asBackground ? self::ARG_AS_BG : ''
        );
        if ($returnCommand) {
            return $command;
        }

        return shell_exec($command);
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

        return shell_exec($command);
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
        $bashScript = '#!/bin/bash'."\n".self::runTask($taskName, false, true);
        $bashTmpFile = sys_get_temp_dir().'/limo_cli_task_'.md5(
                $bashScript
            ).'.sh';
        file_put_contents($bashTmpFile, $bashScript);
        shell_exec(sprintf('chmod +x %s', escapeshellarg($bashTmpFile)));

        return self::runFsWatch($path, $bashTmpFile, $asBackground);
    }

    /**
     * @param string $taskName Task name
     *
     * @return string
     */
    static public function runTaskMdlGulp($taskName)
    {
        $mdlFolder = realpath(
            __DIR__.'/../../../external/material-design-lite'
        );
        $command = sprintf(
            self::COMMAND_MDL_GULP_TASK,
            escapeshellarg($mdlFolder),
            $taskName
        );

        return shell_exec($command);
    }

}
