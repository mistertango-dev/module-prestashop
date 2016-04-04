<?php

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Class MTTransactions
 */
class MTTransactions
{

	/**
	 * @return bool
	 */
	public static function install()
	{
		return Db::getInstance()->Execute(
			'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mttransactions` (
                `id_transaction` varchar(255) NOT NULL,
                `id_order` int(10) NOT NULL,
                `websocket` varchar(255) NULL,
                `amount` DECIMAL(10,2) NOT NULL,
                `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id_transaction`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
		);
	}

	/**
	 * @return bool
	 */
	public static function uninstall()
	{
		return Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'mttransactions`');
	}

	/**
	 * @param $id_transaction
	 * @param $id_order
	 * @param $websocket
	 * @param $amount
	 * @param null $date
	 * @throws PrestaShopDatabaseException
	 */
	public static function insert($id_transaction, $id_order, $websocket, $amount, $date = null)
	{
		$exists = (bool) Db::getInstance()->getValue(
			'SELECT 1 FROM `'._DB_PREFIX_.'mttransactions`
            WHERE `id_transaction` = \''.pSQL($id_transaction).'\''
		);

		if (!$exists) {
			Db::getInstance()->insert(
				'mttransactions',
				array(
					'id_transaction' => pSQL($id_transaction),
					'id_order' => pSQL((int) $id_order),
					'websocket' => pSQL($websocket),
					'amount' => pSQL((float) $amount),
					'payment_date' => isset($date)?date('Y-m-d H:i:s', strtotime($date)):null
				)
			);
		}
	}

	/**
	 * @param $id_transaction
	 * @param $id_order
	 */
	public static function setOrder($id_transaction, $id_order)
	{
		$exists = (bool) Db::getInstance()->getValue(
			'SELECT 1 FROM `'._DB_PREFIX_.'mttransactions`
            WHERE `id_transaction` = \''.pSQL($id_transaction).'\''
		);

		if ($exists) {
			Db::getInstance()->update(
				'mttransactions',
				array(
					'id_order' => pSQL((int) $id_order),
				),
				'id_transaction=\''.pSQL($id_transaction).'\''
			);
		}
	}

	/**
	 * @param $id_transaction
	 * @return false|null|string
	 */
	public static function getOrderIdByTransaction($id_transaction)
	{
		return Db::getInstance()->getValue(
			'SELECT `id_order`
			FROM `'._DB_PREFIX_.'mttransactions`
			WHERE `id_transaction` = \''.pSQL($id_transaction).'\''
		);
	}

	/**
	 * @param $id_order
	 * @return array|bool|null|object
	 */
	public static function getLastForOrder($id_order)
	{
		if (empty($id_order)) {
			return array();
		}

		$transaction = Db::getInstance()->getRow(
			'SELECT * FROM `'._DB_PREFIX_.'mttransactions` WHERE `id_order` = \''.pSQL($id_order).'\''
		);

		return $transaction;
	}
}
