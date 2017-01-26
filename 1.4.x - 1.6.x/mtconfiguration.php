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
    const NAME_ENABLED_CONFIRM_PAGE = 'MT_ENABLED_CONFIRM_PAGE';

    /**
     *
     */
    const NAME_ENABLED_SUCCESS_PAGE = 'MT_ENABLED_SUCCESS_PAGE';

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
    public static function isEnabledConfirmPage()
    {
        return (int)Configuration::get(self::NAME_ENABLED_CONFIRM_PAGE);
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function updateEnabledConfirmPage($value)
    {
        return Configuration::updateValue(self::NAME_ENABLED_CONFIRM_PAGE, $value);
    }

    /**
     * @return mixed
     */
    public static function isEnabledSuccessPage()
    {
        return (int)Configuration::get(self::NAME_ENABLED_SUCCESS_PAGE);
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function updateEnabledSuccessPage($value)
    {
        return Configuration::updateValue(self::NAME_ENABLED_SUCCESS_PAGE, $value);
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
