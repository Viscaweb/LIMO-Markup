<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Watch
 */
class Watch extends Command
{

    protected function configure()
    {
        $this
            ->setName('watch')
            ->setDescription(
                'Compile the project and the dependencies and watch for updates.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = realpath(__DIR__.'/../');
        $scripts = [
            # SCRIPT TO WATCH FOR THE TWIG TEMPLATES
            # => Watch the Twig files to compile them
            new \Scripts\Watch\LivescoreTemplates(),

            # SCRIPT TO WATCH THE MDL COMPONENTS
            # => Watch the Livescore components and move them in the MDL's folder
            new \Scripts\Watch\LivescoreComponents(),
            # => FSWatch on external/mdl/dist/ to move the files
            new \Scripts\Watch\MdlMoveFiles(),
            # => Create and run MDL's Gulp watch all
            # WARNING: This task is not detached, it must be launched at the end
            new \Scripts\Watch\MdlGulpWatchAll(),

        ];

        /** @var \Scripts\Interfaces\ScriptInterface $script */
        foreach ($scripts as $script) {
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

}
