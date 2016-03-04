<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_ . 'mtpayment/mtconfiguration.php');
require_once(_PS_MODULE_DIR_ . 'mtpayment/mttransactions.php');
require_once(_PS_MODULE_DIR_ . 'mtpayment/mtorders.php');
require_once(_PS_MODULE_DIR_ . 'mtpayment/mtcallbacks.php');

/**
 * Class MTPayment
 */
class MTPayment extends PaymentModule
{

    /**
     * @var MTPayment
     */
    private static $instance;

    /**
     * @var array
     */
    private $postErrors = array();

    /**
     *
     */
    public function __construct()
    {
        $this->name = 'mtpayment';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.2';
        $this->author = 'MisterTango';
        $this->is_eu_compatible = 1;

        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('MisterTango Payment');
        $this->description = $this->l('Add MisterTango payment gateway to your shop.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete module with all logs & details?');

        /* Backward compatibility */
        if (_PS_VERSION_ < '1.5') {
            require _PS_MODULE_DIR_ . $this->name . '/backward_compatibility/backward.php';
        }

        self::$instance = $this;
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (
            !parent::install() ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayOrderStateInfo') ||
            !$this->registerHook('payment') ||
            !$this->registerHook('paymentReturn') ||
            !MTTransactions::install() ||
            !MTCallbacks::install() ||
            !MTOrders::insertState()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (
            !parent::uninstall() ||
            !MTTransactions::uninstall() ||
            !MTCallbacks::uninstall() ||
            !MTOrders::deleteState()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return MTPayment
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MTPayment();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $this->postProcess();

        $this->_html = $this->renderForm();

        return $this->_html;
    }

    /**
     *
     */
    public function postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue(MTConfiguration::NAME_USERNAME)) {
                $this->postErrors[] = $this->l('Username is required.');
            }
            if (!Tools::getValue(MTConfiguration::NAME_SECRET_KEY)) {
                $this->postErrors[] = $this->l('Secret key is required.');
            }
        }
    }

    /**
     * @return string
     */
    public function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            MTConfiguration::updateUsername(Tools::getValue(MTConfiguration::NAME_USERNAME));
            MTConfiguration::updateSecretKey(Tools::getValue(MTConfiguration::NAME_SECRET_KEY));
        }

        return $this->displayConfirmation($this->l('Settings updated'));
    }

    /**
     * @return string
     */
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('CONFIGURATION'),
                    'icon' => 'icon-cog',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Username'),
                        'name' => MTConfiguration::NAME_USERNAME,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Secret key'),
                        'name' => MTConfiguration::NAME_SECRET_KEY,
                        'required' => true,
                    ),
                ),
                'submit' => array(
                    'name' => 'btnSubmit',
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                : 0;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex =
            $this->context->link->getAdminLink('AdminModules', false)
            . '&configure='
            . $this->name . '&tab_module='
            . $this->tab . '&module_name='
            . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getFormFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    /**
     * @return array
     */
    private function getFormFieldsValues()
    {
        return array(
            MTConfiguration::NAME_USERNAME => Tools::getValue(
                MTConfiguration::NAME_USERNAME,
                MTConfiguration::getUsername()
            ),
            MTConfiguration::NAME_SECRET_KEY => Tools::getValue(
                MTConfiguration::NAME_SECRET_KEY,
                MTConfiguration::getSecretKey()
            ),
        );
    }

    /**
     * @param $params
     */
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/mtpayment.css');

        $this->context->controller->addJS($this->_path . 'views/js/mtpayment.js');

        $this->smarty->assign(array(
            'mtpayment_username' => MTConfiguration::getUsername(),
            'mtpayment_url_validate_order' => Context::getContext()->link->getModuleLink(
                'mtpayment',
                'validate-order'
            ),
            'mtpayment_url_validate_transaction' => Context::getContext()->link->getModuleLink(
                'mtpayment',
                'validate-transaction'
            ),
            'mtpayment_url_order_states' => Context::getContext()->link->getModuleLink(
                'mtpayment',
                'order-states'
            ),
        ));

        return $this->display(__FILE__, 'header.tpl');
    }

    /**
     * @param $params
     */
    public function hookDisplayOrderStateInfo($params)
    {
        $id_order_state_pending = MTConfiguration::getOsPending();
        $allow_different_payment = true;
        $order = $params['order'];
        $cart = new Cart($order->id_cart);
        $history = $order->getHistory($this->context->language->id);

        foreach ($history as &$order_state) {
            if ($order_state['id_order_state'] != $id_order_state_pending) {
                $allow_different_payment = false;
            }
        }

        if ($params['id_order_state'] != $id_order_state_pending) {
            $allow_different_payment = false;
        }

        $this->assignTemplateAssets($this->smarty, $cart);

		$transaction = MTTransactions::getLastForOrder($order->id);
		
        $this->smarty->assign(array(
            'order' => $order,
            'websocket' => isset($transaction['websocket'])?$transaction['websocket']:null,
            'allow_different_payment' => $allow_different_payment,
        ));

        return $this->display(__FILE__, 'order-state-info.tpl');
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function hookPayment($params)
    {
        $this->assignTemplateAssets($this->smarty, $params['cart']);

        return $this->display(__FILE__, 'payment.tpl');
    }

    /**
     * @param $params
     */
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        switch ($params['objOrder']->getCurrentState()) {
            case _PS_OS_PAYMENT_:
                $this->smarty->assign('status', 'ok');
                break;
            case _PS_OS_OUTOFSTOCK_:
                $this->smarty->assign('status', 'ok');
                break;

            case _PS_OS_BANKWIRE_:
                $this->smarty->assign('status', 'pending');
                break;

            case _PS_OS_ERROR_:
            default:
                $this->smarty->assign('status', 'failed');
                break;
        }

        return $this->display(__FILE__, 'confirmation.tpl');
    }

    /**
     * @param $smarty
     * @param $cart
     */
    public function assignTemplateAssets($smarty, $cart)
    {
        $currency = new Currency((int)$cart->id_currency);

        $smarty->assign(array(
            'mtpayment_version' => $this->version,
            'mtpayment_path' => $this->_path,
            'customer_email' => $this->getContextCustomer()->email,
            'transaction' => $cart->id . '_' . time(),
            'cart_currency_iso_code' => $currency->iso_code,
            'amount' => number_format($cart->getOrderTotal(), 2, '.', ''),
        ));
    }

    /**
     * @return mixed
     */
    public function getContextCart()
    {
        return $this->context->cart;
    }

    /**
     * @param $id_cart
     */
    public function setContextCart($id_cart)
    {
        $this->context->cart = new Cart($id_cart);
    }

    /**
     * @return mixed
     */
    public function getContextCustomer()
    {
        return $this->context->customer;
    }

    /**
     * @param $id_customer
     */
    public function setContextCustomer($id_customer)
    {
         $this->context->customer = new Customer($id_customer);
    }
}
