<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'mtpayment/mtpayment.php');

/**
 * Presta 1.4 compatibility
 */
class MtPayment_1_4
{
    const VIEWS_PATH = 'backward_compatibility/1.4/views';

    /**
     * @return string
     */
    public static function renderForm()
    {
        $module = MTPayment::getInstance();
        $smarty = $module->context->smarty;

        $fields = array(
            'username' => array(
                'name' => MTConfiguration::NAME_USERNAME,
                'value' => MTConfiguration::getUsername(),
            ),
            'secret_key' => array(
                'name' => MTConfiguration::NAME_SECRET_KEY,
                'value' => MTConfiguration::getSecretKey(),
            ),
            'callback_url' => array(
                'name' => MTConfiguration::NAME_CALLBACK_URL,
                'value' => MTConfiguration::getCallbackUrl(),
            ),
        );

        $smarty->assign('views_path', '/modules/mtpayment/backward_compatibility/1.4/views');
        $smarty->assign('fields', $fields);

        die($module->display('mtpayment.php', self::VIEWS_PATH.'/templates/back/settings.tpl'));

        return $module->display('mtpayment.php', self::VIEWS_PATH.'/templates/back/settings.tpl');
    }

    /**
     * @param $controller
     * @return string
     */
    public static function getControllerLink($controller)
    {
        $values = array('controller' => $controller);

        return _PS_BASE_URL_.'/modules/mtpayment/controllers.php'.'?'.http_build_query($values);
    }

    /**
     * @return string
     */
    public static function renderPayButton()
    {
        $module = MTPayment::getInstance();

        $module->smarty->assign('confirm_link', self::getControllerLink('validation'));

        return $module->display('mtpayment.php', self::VIEWS_PATH.'/templates/hook/payment.tpl');
    }
}
