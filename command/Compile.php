<?php
use Scripts\Exceptions\CommandNotAvailableException;
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
        $scripts = [new Scripts\Compile\Templates()];

        /** @var \Scripts\Interfaces\ScriptInterface $script */
        foreach ($scripts as $script) {
            $scriptMessage = str_pad($script->getDescription(), 80, ' ');
            $output->write($scriptMessage);

            $breakScript = false;
            $timeBegin = microtime(true);
            try {
                $scriptResult = $script->execute($input, $output, $basePath);
                if ($scriptResult) {
                    $scriptTextResult = '<info>SUCCESS!</info>';
                } else {
                    $scriptTextResult = '<error>ERROR (no details)!</error>';
                }
            } catch (CommandNotAvailableException $ex){
                $scriptTextResult = sprintf(
                    '<info>%s</info>',
                    $ex->getMessage()
                );
            } catch (\Exception $ex) {
                $scriptTextResult = sprintf(
                    '<error>%s</error>',
                    $ex->getMessage()
                );
                $breakScript = true;
            }
            $timeEnd = microtime(true);

            $time = round($timeEnd - $timeBegin, 2);
            $output->write(str_pad(sprintf('Time: %ss', $time), 20, ' '));

            $output->writeln($scriptTextResult);
            if ($breakScript){
                break;
            }
        }

    }

}
