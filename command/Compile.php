<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            new Scripts\InstallMdl(),
            new \Scripts\Components\Livescore()
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
                $scriptTextResult = $ex->getMessage();
            }
            $output->writeln($scriptTextResult);
        }

    }

}
