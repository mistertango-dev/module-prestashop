<?php

require_once(dirname(__FILE__) . '/../../mtpayment.php');

/**
 * Class MTPaymentValidationModuleFrontController
 */
class MTPaymentValidationModuleFrontController extends ModuleFrontController
{
    /**
     *
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        if (
            $cart->id_customer == 0
            || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0
            || !$this->module->active
        ) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);

        $this->module->validateOrder(
            $cart->id,
            MTConfiguration::getOsPending(),
            $total,
            $this->module->displayName,
            null,
            array(),
            (int)$currency->id,
            false,
            $customer->secure_key
        );

        $order = Order::getByCartId($cart->id);

        Tools::redirect(
            $this->context->link->getModuleLink(
                'mtpayment',
                'order-states',
                array(
                    'id_order' => $order->id,
                )
            )
        );
    }
}
