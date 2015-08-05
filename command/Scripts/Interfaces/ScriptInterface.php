<?php
namespace Scripts\Interfaces;

use Scripts\Exceptions\CommandNotAvailableException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface ScriptInterface
{

    /**
     * Return the description of the task.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Execute the script
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $basePath
     *
     * @throws \Exception
     * @throws CommandNotAvailableException
     *
     * @return bool
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output,
        $basePath
    );

}