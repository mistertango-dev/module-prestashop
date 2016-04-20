<?php
/**
 * Presta 1.4 compatibility
 */

Class MtPayment_1_4 {
    const VIEWS_PATH =  'backward_compatibility/1.4/views';

    /**
     * @return string
     */
    public static function renderForm()
    {
        $module = MTPayment::getInstance();
        $smarty = $module->context->smarty;

        $fields = array(
            'username'  => array(
                'name' => MTConfiguration::NAME_USERNAME,
                'value' => MTConfiguration::getUsername()
            ),
            'secret_key' => array(
                'name' => MTConfiguration::NAME_SECRET_KEY,
                'value' => MTConfiguration::getSecretKey()
            ),
            'enable_confirm_page' => array(
                'name' => MTConfiguration::NAME_ENABLED_CONFIRM_PAGE,
                'value' => MTConfiguration::isEnabledConfirmPage()
            )
        );

        $smarty->assign('views_path', '/modules/mtpayment/backward_compatibility/1.4/views');
        $smarty->assign('fields', $fields);

        return $module->display('../mtpayment.php', self::VIEWS_PATH . '/templates/back/settings.tpl');
    }

    /**
     * @param $controller
     * @return string
     */
    public static function getControllerLink ($controller)
    {
        $values = array('controller' => $controller);
        return _PS_BASE_URL_ . '/modules/mtpayment/controllers.php' . '?' . http_build_query($values);
    }

    /**
     * @return string
     */
    public static function renderPayButton()
    {
        $module = MTPayment::getInstance();

        $module->smarty->assign('confirm_link', self::getControllerLink('confirm'));

        return $module->display('../mtpayment.php', self::VIEWS_PATH . '/templates/hook/payment.tpl');
    }

    /**
     * @return string
     */
    public static function renderHeader()
    {
        $module = MTPayment::getInstance();

        $module->context->controller->addCSS('/modules/mtpayment/backward_compatibility/1.4/views/css/mtpayment.css');

        $module->context->controller->addJS('/modules/mtpayment/views/js/mtpayment.js');

        $module->smarty->assign(array(
            'mtpayment_username' => MTConfiguration::getUsername(),
            'mtpayment_enabled_confirm_page' => MTConfiguration::isEnabledConfirmPage(),
            'mtpayment_url_validate_order' => self::getControllerLink('validate-order'),
            'mtpayment_url_validate_transaction' => self::getControllerLink('validate-transaction'),
            'mtpayment_url_order_states' =>  self::getControllerLink('order-states')
        ));

        return $module->display('../mtpayment.php', 'views/templates/hook/header.tpl');
    }
}