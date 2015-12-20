<?php

require_once(dirname(__FILE__) . '/../../mtpayment.php');

/**
 * Class MTPaymentValidateTransactionModuleFrontController
 */
class MTPaymentValidateTransactionModuleFrontController extends ModuleFrontController
{

    /**
     *
     */
    public function initContent()
    {
        parent::initContent();

        if (!Validate::isLoadedObject(MTPayment::getInstance()->getContextCustomer())) {
            die(Tools::jsonEncode(array(
                'success' => false,
                'error' => $this->module->l('Invalid customer', 'mtpayment'),
            )));
        }

        $id_order = Tools::getValue('order');
        $id_transaction = Tools::getValue('transaction');
        $websocket = Tools::getValue('websocket');
        $amount = Tools::getValue('amount');

        $order = new Order($id_order);

        if (Validate::isLoadedObject($order)) {
            MTTransactions::insert($id_transaction, $order->id, $websocket, $amount);

            die(Tools::jsonEncode(array(
                'success' => true,
                'id_order' => $order->id,
            )));
        }

        die(Tools::jsonEncode(array(
            'success' => false,
            'error' => $this->module->l('Invalid order', 'mtpayment'),
        )));
    }
}
