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
        $order = new Order($id_order);
        $cart = new Cart($order->id_cart);
        $customer = new Customer($cart->id_customer);

        if (_PS_VERSION_ < '1.5') {
            $urlSuccessPage =
                __PS_BASE_URI__
                .'order-confirmation.php?'
                .'id_cart='.$cart->id
                .'&id_module='.MTPayment::getInstance()->getId()
                .'&id_order='.$order->id
                .'&key='.$customer->secure_key;
        } else {
            $urlSuccessPage = Context::getContext()->link->getPageLink(
                'order-confirmation',
                null,
                null,
                array(
                    'id_cart' => $cart->id,
                    'id_module' => MTPayment::getInstance()->getId(),
                    'id_order' => $order->id,
                    'key' => $customer->secure_key,
                )
            );
        }

        if (isset($id_order)) {
            die(Tools::jsonEncode(array(
                'success' => true,
                'order' => $id_order,
                'url_success_page' => $urlSuccessPage
            )));
        }

        die(Tools::jsonEncode(array(
            'success' => false,
            'error' => $this->module->l('Transaction is invalid', 'mtpayment'),
        )));
    }
}
