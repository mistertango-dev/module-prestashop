<?php

require_once(dirname(__FILE__) . '/../../mtpayment.php');

/**
 * Class MisterTangoOrdersModuleFrontController.
 */
class MTPaymentValidateOrderModuleFrontController extends ModuleFrontController
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
                'error' => $this->module->l('You aren\'t logged in', 'mtpayment'),
            )));
        }

        $id_transaction = Tools::getValue('transaction');
        $websocket = Tools::getValue('websocket');
        $amount = Tools::getValue('amount');

        $id_order = MTOrders::open($id_transaction, $amount, $websocket);

        if (isset($id_order)) {
            die(Tools::jsonEncode(array(
                'success' => true,
                'order' => $id_order
            )));
        }

        die(Tools::jsonEncode(array(
            'success' => false,
            'error' => $this->module->l('Transaction is invalid', 'mtpayment'),
        )));
    }
}
