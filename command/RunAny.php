<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunAny
 */
class RunAny extends Command
{

    protected function configure()
    {
        $this
            ->setName('run-any')
            ->setDescription(
                'Run any given task.'
            )
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'What\'s the task?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandName = $input->getArgument('name');
        $fullCommandName = 'Scripts\\'.$commandName;

        if (!class_exists($fullCommandName)) {
            throw new \Exception(
                sprintf(
                    'The given command does not exists (%s).',
                    $fullCommandName
                )
            );
        }

        /** @var \Scripts\Interfaces\ScriptInterface $script */
        $script = new $fullCommandName($input, $output);

        $requiredInterface = 'Scripts\Interfaces\ScriptInterface';
        if (!in_array($requiredInterface, class_implements($script))) {
            throw new \Exception(
                'The given command is not allowed to be launched'
            );
        }
        $basePath = realpath(__DIR__.'/../');

        $scriptMessage = str_pad($script->getDescription(), 80, ' ');
        $output->write($scriptMessage);

        $timeBegin = microtime(true);
        try {
            $scriptResult = $script->execute($input, $output, $basePath);
            if ($scriptResult) {
                $scriptTextResult = '<info>SUCCESS!</info>';
            } else {
                $scriptTextResult = '<error>ERROR (no details)!</error>';
            }
        } catch (\Exception $ex) {
            $scriptTextResult = sprintf(
                '<error>%s</error>',
                $ex->getMessage()
            );
        }
        $timeEnd = microtime(true);

        $time = round($timeEnd - $timeBegin, 2);
        $output->write(str_pad(sprintf('Time: %ss', $time), 20, ' '));

        $output->writeln($scriptTextResult);

    }

}
