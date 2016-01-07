<?php

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/mttools.php');
require_once(dirname(__FILE__) . '/mtpayment.php');

Db::getInstance()->execute('SHOW TABLES LIKE \''._DB_PREFIX_.'transactions_mistertango\'');
$existsTransactions = Db::getInstance()->numRows() > 0;
Db::getInstance()->execute('SHOW TABLES LIKE \''._DB_PREFIX_.'callbacks_mistertango\'');
$existsCallbacks = Db::getInstance()->numRows() > 0;
if (empty($existsTransactions)) {
    echo 'Transactions table was not found.<br />';
} else {
    $transactions = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'transactions_mistertango`');

    try {
        foreach ($transactions as $transaction) {
            MTTransactions::insert(
                $transaction['id_transaction'],
                $transaction['id_order'],
                $transaction['id_websocket'],
                $transaction['amount'],
                $transaction['payment_date']
            );
        }
    } catch (Exception $e) {

    }
}

if (empty($existsCallbacks)) {
    echo 'Callbacks table was not found.<br />';
} else {
    $callbacks = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'callbacks_mistertango`');

    try {
        foreach ($callbacks as $callback) {
            MTCallbacks::insert(
                $callback['uuid_callback'],
                $callback['id_transaction'],
                $callback['amount'],
                $callback['callback_date']
            );
        }
    } catch (Exception $e) {

    }
}

if (empty($existsTransactions) && empty($existsCallbacks)) {
    echo 'No tables were found. Data collecting has failed.<br />';
} elseif (empty($existsTransactions) || empty($existsCallbacks)) {
    echo 'Not all table were found. Only some data were collected.<br />';
} else {
    echo 'Data was successfully collected.<br />';
}

