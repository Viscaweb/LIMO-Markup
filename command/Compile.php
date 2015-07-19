<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Compile
 */
class Compile extends Command
{

    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Compile the project and the dependencies.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = realpath(__DIR__.'/../');
        $scripts = [
            new Scripts\Compile\Clean(),
            new Scripts\Mdl\Install(),
            new Scripts\Mdl\Reset(),
            new Scripts\Components\Livescore(),
            new Scripts\Components\Mdl(),
            new Scripts\Mdl\Compile(),
            new Scripts\Compile\MoveFiles()
        ];

        /** @var \Scripts\Interfaces\ScriptInterface $script */
        foreach ($scripts as $script) {
            $scriptMessage = str_pad($script->getDescription(), 80, ' ');
            $output->write($scriptMessage);

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
            $output->writeln($scriptTextResult);
        }

    }

}
