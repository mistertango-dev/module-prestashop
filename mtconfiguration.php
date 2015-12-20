<?php

if (!defined('_PS_VERSION_'))
    exit;

/**
 * Class MTTools
 */
class MTConfiguration
{

    /**
     *
     */
    const NAME_USERNAME = 'MT_USERNAME';

    /**
     *
     */
    const NAME_SECRET_KEY = 'MT_SECRET_KEY';

    /**
     *
     */
    const NAME_OS_PENDING = 'MT_OS_PENDING';

    /**
     * @return mixed
     */
    public static function getUsername()
    {
        return Configuration::get(self::NAME_USERNAME);
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function updateUsername($value)
    {
        return Configuration::updateValue(self::NAME_USERNAME, $value);
    }

    /**
     * @return mixed
     */
    public static function getSecretKey()
    {
        return Configuration::get(self::NAME_SECRET_KEY);
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function updateSecretKey($value)
    {
        return Configuration::updateValue(self::NAME_SECRET_KEY, $value);
    }

    /**
     * @return mixed
     */
    public static function getOsPending()
    {
        return Configuration::get(self::NAME_OS_PENDING);
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function updateOsPending($value)
    {
        return Configuration::updateValue(self::NAME_OS_PENDING, $value);
    }
}
