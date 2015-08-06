<?php
namespace Scripts\Helper;

/**
 * Class System
 */
class System
{

    /**
     * @return bool
     */
    static public function isUbuntu()
    {
        return (self::getOs() == 'Linux');
    }

    /**
     * @return bool
     */
    static public function isMac()
    {
        return (self::getOs() == 'Darwin');
    }

    /**
     * @return string
     */
    static public function getOs()
    {
        return PHP_OS;
    }

}
