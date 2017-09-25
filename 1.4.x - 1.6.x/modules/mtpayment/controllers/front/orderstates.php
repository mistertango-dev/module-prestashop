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
        if ($order->getCurrentState() == _PS_OS_PAYMENT_) {
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

        $this->context->controller->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/mtpayment.js');
        $this->context->controller->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/order-states.js');

        $this->assignTemplateAssets($this->context->smarty, $order, $cart);

				if (!Validate::isLoadedObject($this->context->customer) || $this->context->customer->id != $order->id_customer) {
            die($this->module->l('You aren\'t logged in', 'mtpayment'));
        }

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

        $id_order = Tools::getValue('id_order');

        $order = new Order($id_order);

        if (Validate::isLoadedObject($order)) {
            $cart = new Cart($order->id_cart);
            $customer = new Customer($cart->id_customer);

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
            if ($order->getCurrentState() == _PS_OS_PAYMENT_) {
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
     * @param Order $order
     * @param Cart $cart
     */
    private function assignTemplateAssets($smarty, Order $order, Cart $cart)
    {
        $history = null;
        $customer = new Customer($cart->id_customer);

        if (Validate::isLoadedObject($order)) {
            $history = $order->getHistory($this->context->language->id);
            foreach ($history as &$order_state) {
                $order_state['text-color'] = 'black';
                if (isset($order_state['color'])) {
                    $order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white' : 'black';
                }
            }
        }

        $smarty->assign(array(
            'mtpayment_username' => MTConfiguration::getUsername(),
            'mtpayment_callback_url' => MTConfiguration::getCallbackUrl(true),
            'mtpayment_path' => MTPayment::getInstance()->getPath(),
            'url_order_states' => $this->context->link->getModuleLink(
                'mtpayment',
                'order-states',
                array(
                    'id_order' => $order->id
                )
            ),
            'url_order_confirmation' => Context::getContext()->link->getPageLink(
                'order-confirmation',
                null,
                null,
                array(
                    'id_cart' => $cart->id,
                    'id_module' => MTPayment::getInstance()->getId(),
                    'id_order' => $order->id,
                    'key' => $customer->secure_key,
                )
            ),
            'currency' => new Currency($order->id_currency),
            'customer' => $customer,
            'auto_open' => Tools::getValue('auto') === 'open',
            'order' => $order,
            'history' => $history,
            'transaction_id' => $order->id . '_' . date('YmdHis'),
        ));
    }
}
