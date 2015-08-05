<?php

namespace Scripts\Exceptions;

use Scripts\Helper\System;

/**
 * Class CommandNotAvailableException
 */
class CommandNotAvailableException extends \Exception
{
    const messageTemplate = 'The command "%s" is not available on your system (%s). Extra informations: %s';

    /**
     * @var string Command not available
     */
    private $command;

    /**
     * CommandNotAvailableException constructor.
     *
     * @param string $command
     * @param string $extraInformation
     */
    public function __construct($command, $extraInformation = '')
    {
        $this->command = $command;

        $message = sprintf(
            self::messageTemplate,
            $command,
            System::getOs(),
            empty($extraInformation) ? 'none' : $extraInformation
        );

        parent::__construct($message);
    }


}
