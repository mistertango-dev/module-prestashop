<?php

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/mttools.php');
require_once(dirname(__FILE__) . '/mtpayment.php');

$hash = Tools::getValue('hash');

if ($hash !== false) {
    $data = Tools::jsonDecode(MTTools::decrypt($hash, Configuration::get(MTConfiguration::NAME_SECRET_KEY)));

    if (isset($data)) {
        $data->custom = isset($data->custom) ? Tools::jsonDecode($data->custom) : null;
    }

    if (!isset($data->custom) && !isset($data->custom->description)) {
        die('Error occurred: Data is not set or transaction ID is not present');
    }

    $message = '';
    $transaction = explode('_', $data->custom->description);

    if (count($transaction) != 2) {
        die('Error occurred: Transaction ID is corrupted');
    }

    $order = new Order($transaction[0]);
    if (!Validate::isLoadedObject($order)) {
        die('Error occurred: Such order does not exist');
    }

    $transactionAmount = bcdiv($data->custom->data->amount, 1, 2);
    $orderTotalPaid = bcdiv($order->total_paid, 1, 2);
    if ($transactionAmount !== $orderTotalPaid) {
        die('Error occurred: Payment amount does not match to grand total');
    }

    if ($order->getCurrentState() == MTConfiguration::getOsPending() || $order->getCurrentState() == (int)Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')) {
        try {
            MTOrders::close($order, $transactionAmount);
        } catch (Exception $e) {
            die('Error occurred: ' . $e->getMessage());
        }
    }

    die('OK');
}

die('Error occurred: Hash is empty');
