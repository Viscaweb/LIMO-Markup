<?php

namespace Scripts\Exceptions;

/**
 * Class GulpCouldNotCompileException
 */
class GulpCouldNotCompileException extends \Exception
{
    const messageTemplate = 'Gulp could not compiled and sent this message: %s';

    /**
     * @param string $gulpErrorMessage
     */
    public function __construct($gulpErrorMessage)
    {
        $message = sprintf(
            self::messageTemplate,
            $gulpErrorMessage
        );

        parent::__construct($message);
    }


}
