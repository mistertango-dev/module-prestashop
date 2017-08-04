<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'mtpayment/mtpayment.php');

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

        require_once dirname(__FILE__).'/order_states.php';

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
     * @param Order $order
     * @param $amount
     * @throws Exception
     */
    public static function close(Order $order, $amount)
    {
        MTPayment::getInstance()->setContextCart(Order::getCartIdStatic($order->id));
        MTPayment::getInstance()->setContextCustomer(MTPayment::getInstance()->getContextCart()->id_customer);

        $history = $order->getHistory($order->id_lang);

        if (empty($history)) {
            throw new \Exception('Order history is not found');
        }

        $order->addOrderPayment((float)$amount, MTPayment::getInstance()->displayName, $order->id);
        $order->save();

        $new_history = new OrderHistory();
        $new_history->id_order = (int)$order->id;
        $new_history->changeIdOrderState((int)_PS_OS_PAYMENT_, $order->id, true);
        $new_history->addWithemail(true, array());
    }
}
