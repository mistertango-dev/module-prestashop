<?php

if (!defined('_PS_VERSION_'))
    exit;

require_once(_PS_MODULE_DIR_ . 'mtpayment/mtpayment.php');

/**
 * Class MTOrders
 */
class MTOrders
{
    /**
     * @return bool
     */
    public static function insertState()
    {
        $order_state = new OrderState();
        $order_states_names = array();

        require_once dirname(__FILE__).'/translations/order_states.php';

        foreach (Language::getLanguages(false) as $language) {
            $name =
                isset($order_states_names[$language['iso_code']])
                    ? $order_states_names[$language['iso_code']]
                    : $order_states_names['en'];
            $order_state->name[$language['id_lang']] = $name;
        }

        $order_state->module_name = MTPayment::getInstance()->name;
        $order_state->send_email = 0;
        $order_state->color = '#4169E1';
        $order_state->unremovable = 1;
        $order_state->logable = 0;

        $order_state->getFieldsLang();

        $order_state->save();

        copy(dirname(__FILE__).'/logo.gif', _PS_IMG_DIR_.'os/'.$order_state->id.'.gif');

        MTConfiguration::updateOsPending($order_state->id);

        return true;
    }

    /**
     * @return bool
     */
    public static function deleteState()
    {
        $id_order_state = MTConfiguration::getOsPending();

        $order_state = new OrderState($id_order_state);

        unlink(_PS_IMG_DIR_.'os/'.$order_state->id.'.gif');

        $order_state->delete();

        return true;
    }

    /**
     * @param $id_transaction
     * @param $amount
     * @param null $websocket
     * @return bool
     */
    public static function open($id_transaction, $amount, $websocket = null)
    {
        $result = false;
        $transaction = explode('_', $id_transaction);

        if (count($transaction) == 2) {
            $id_cart = $transaction[0];

            $cart = new Cart($id_cart);

            if (Validate::isLoadedObject($cart) && !$cart->OrderExists()) {
                MTPayment::getInstance()->validateOrder(
                    (int) $id_cart,
                    (int) Configuration::get(MTConfiguration::NAME_OS_PENDING),
                    (float) 0,
                    MTPayment::getInstance()->displayName,
                    '',
                    array(),
                    null,
                    true
                );
            }

            $id_order = Order::getOrderByCartId($id_cart);

            if ($id_order !== false) {
                MTTransactions::insert($id_transaction, $id_order, $websocket, $amount);
                if (isset(MTPayment::getInstance()->pcc)) {
                    MTPayment::getInstance()->pcc->transaction_id = $id_transaction;
                }
            }

            $result = true;
        }

        return $result;
    }

    /**
     * @param $id_transaction
     * @param $amount
     * @return bool
     */
    public static function close($id_transaction, $amount)
    {
        $id_order = MTTransactions::getOrderIdByTransaction($id_transaction);

        if (empty($id_order)) {
            self::open($id_transaction, $amount);

            $id_order = MTTransactions::getOrderIdByTransaction($id_transaction);
        }

        MTPayment::getInstance()->setContextCart(Order::getCartIdStatic($id_order));
        MTPayment::getInstance()->setContextCustomer(MTPayment::getInstance()->getContextCart()->id_customer);

        $order = new Order($id_order);
        $history = $order->getHistory($order->id_lang);

        if (empty($history)) {
            return false;
        }

        if (!Validate::isLoadedObject($order)) {
            //@todo: exception should be logged.

            return false;
        }

        $state = Configuration::get('PS_OS_PAYMENT');

        $total_paid_real = $order->total_paid_real + $amount;

        if (bcdiv($order->total_paid, 1, 2) != bcdiv($total_paid_real, 1, 2)) {
            $state = Configuration::get('PS_OS_ERROR');
        }

        $order->addOrderPayment((float) $amount, MTPayment::getInstance()->displayName, $id_transaction);
        $order->save();

        $new_history = new OrderHistory();
        $new_history->id_order = (int) $order->id;
        $new_history->changeIdOrderState((int) $state, $order, true);
        $new_history->addWithemail(true, array());

        return true;
    }
}
