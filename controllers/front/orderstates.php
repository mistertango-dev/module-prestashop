<?php

require_once(dirname(__FILE__) . '/../../mtpayment.php');

/**
 * Class MTPaymentOrderStatesModuleFrontController
 */
class MTPaymentOrderStatesModuleFrontController extends ModuleFrontController
{

    /**
     * @var bool
     */
    public $ssl = true;

    /**
     * @var bool
     */
    public $display_column_left = false;

    /**
     *
     */
    public function initContent()
    {
        parent::initContent();

        if (Tools::getValue('ajax', false)) {
            $this->ajaxProcessGetHtmlTable();
        }

        if (!$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $order = new Order(Tools::getValue('id_order'));

        if (!Validate::isLoadedObject($order)) {
            die($this->module->l('Order is invalid.', 'mtpayment'));
        }

        $cart = new Cart($order->id_cart);
        $customer = new Customer($cart->id_customer);
        if ($order->getCurrentState() == _PS_OS_PAYMENT_ && MTConfiguration::isEnabledSuccessPage()) {
            Tools::redirect(Context::getContext()->link->getPageLink(
                'order-confirmation',
                null,
                null,
                array(
                    'id_cart' => $cart->id,
                    'id_module' => $this->module->id,
                    'id_order' => $order->id,
                    'key' => $customer->secure_key,
                )
            ));
        }

        MTPayment::getInstance()->assignTemplateAssets($this->context->smarty, $cart);
        $this->context->controller->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/order-states.js');

        $this->assignTemplateAssets($this->context->smarty, $order, $cart);

        $this->setTemplate('order-states.tpl');
    }

    /**
     *
     */
    public function ajaxProcessGetHtmlTable()
    {
        if (!Validate::isLoadedObject($this->context->customer)) {
            die(Tools::jsonEncode(array(
                'success' => false,
                'error' => $this->module->l('You aren\'t logged in', 'mtpayment'),
            )));
        }

        $id_order = Tools::getValue('order');

        $order = new Order($id_order);

        if (Validate::isLoadedObject($order)) {
            $cart = new Cart($order->id_cart);
            $customer = new Customer($cart->id_customer);

            MTPayment::getInstance()->assignTemplateAssets($this->context->smarty, $cart);
            $this->assignTemplateAssets($this->context->smarty, $order, $cart);

            $path_table_order_states =
                _PS_MODULE_DIR_
                . $this->module->name
                . '/views/templates/front/order-states-table.tpl';

            $path_themes_table_order_states =
                _PS_THEME_DIR_
                . 'modules/'
                . $this->module->name
                . '/views/templates/front/order-states-table.tpl';

            if (file_exists($path_themes_table_order_states)) {
                $path_table_order_states = $path_themes_table_order_states;
            }

            $redirect = false;
            if ($order->getCurrentState() == _PS_OS_PAYMENT_ && MTConfiguration::isEnabledSuccessPage()) {
                $redirect = Context::getContext()->link->getPageLink(
                    'order-confirmation',
                    null,
                    null,
                    array(
                        'id_cart' => $cart->id,
                        'id_module' => $this->module->id,
                        'id_order' => $order->id,
                        'key' => $customer->secure_key,
                    )
                );
            }

            die(Tools::jsonEncode(array(
                'success' => true,
                'html' => $this->context->smarty->fetch($path_table_order_states),
                'redirect' => $redirect,
            )));
        }

        die(Tools::jsonEncode(array(
            'success' => false,
            'error' => $this->module->l('Order is invalid', 'mtpayment'),
        )));
    }

    /**
     * @param $smarty
     * @param $order
     * @param $cart
     */
    private function assignTemplateAssets($smarty, $order, $cart)
    {
        $id_order_state_pending = MTConfiguration::getOsPending();
        $history = null;
        $allow_different_payment = true;

        if (Validate::isLoadedObject($order)) {
            $history = $order->getHistory($this->context->language->id);
            foreach ($history as &$order_state) {
                $order_state['text-color'] = 'black';
                if (isset($order_state['color'])) {
                    $order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white' : 'black';
                }
                if ($order_state['id_order_state'] != $id_order_state_pending) {
                    $allow_different_payment = false;
                }
            }
        }
		
		$transaction = MTTransactions::getLastForOrder($order->id);

        $smarty->assign(array(
            'order' => $order,
            'history' => $history,
            'websocket' => isset($transaction['websocket'])?$transaction['websocket']:null,
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'id_order_state_pending' => $id_order_state_pending,
            'allow_different_payment' => $allow_different_payment,
        ));
    }
}
