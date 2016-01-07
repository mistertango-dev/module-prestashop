<?php

if (!defined('_PS_VERSION_'))
    exit;

/**
 * Class MTCallbacks
 */
class MTCallbacks
{

    /**
     * @return bool
     */
    public static function install()
    {
        return Db::getInstance()->Execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mtcallbacks` (
                `callback` VARCHAR(255) NOT NULL,
                `id_transaction` VARCHAR(255) NOT NULL,
                `amount` DECIMAL(10,2) NOT NULL,
                `callback_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`callback`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );
    }

    /**
     * @return bool
     */
    public static function uninstall()
    {
        return Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'mtcallbacks`');
    }

    /**
     * @param $callback
     * @return bool
     */
    public static function exists($callback)
    {
        $has_duplicate = (bool) Db::getInstance()->getValue(
            'SELECT 1 FROM `'._DB_PREFIX_.'mtcallbacks`
            WHERE `callback` = \''.pSQL($callback).'\''
        );

        return $has_duplicate;
    }

    /**
     * @param $callback
     * @param $id_transaction
     * @param $amount
     * @param null $date
     * @throws PrestaShopDatabaseException
     */
    public static function insert($callback, $id_transaction, $amount, $date = null)
    {
        Db::getInstance()->insert(
            'mtcallbacks',
            array(
                'callback' => pSQL($callback),
                'id_transaction' => pSQL($id_transaction),
                'amount' => pSQL($amount),
                'callback_date' => isset($date)?date('Y-m-d H:i:s', strtotime($date)):null
            )
        );
    }
}
