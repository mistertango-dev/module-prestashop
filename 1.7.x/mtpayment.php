<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

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
        $this->author = 'MisterTango';
        $this->version = '1.3.0';
        $this->need_instance = 1;

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->controllers = array('confirm', 'orderstates', 'validateorder', 'validatetransaction');
        $this->is_eu_compatible = 1;
        $this->currencies = false;

        parent::__construct();

        $this->displayName = $this->trans('Payments via MisterTango', array(), 'Modules.MTPayment.Admin');
        $this->description = $this->trans('MisterTango payments gateway', array(), 'Modules.MTPayment.Admin');

        self::$instance = $this;
    }

    /**
     * @return bool
     */
    public function install()
    {
        $hooks = array(
            'payment',
            'paymentReturn',
            'paymentOptions',
        );

        if (
            !parent::install() ||
            !$this->registerHooks($hooks) ||
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
    private function registerHooks($hooks)
    {
        foreach($hooks AS $hook) {
            if(!$this->registerHook($hook)) {
                return false;
            }
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
            MTConfiguration::updateEnabledConfirmPage(Tools::getValue(MTConfiguration::NAME_ENABLED_CONFIRM_PAGE));
            MTConfiguration::updateEnabledSuccessPage(Tools::getValue(MTConfiguration::NAME_ENABLED_SUCCESS_PAGE));
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
                    array(
                        'type' => 'select',
                        'label' => $this->l('Enable standard mode'),
                        'name' => MTConfiguration::NAME_ENABLED_CONFIRM_PAGE,
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Yes')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Enable success page'),
                        'name' => MTConfiguration::NAME_ENABLED_SUCCESS_PAGE,
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_option' => 0,
                                    'name' => $this->l('No')
                                ),
                                array(
                                    'id_option' => 1,
                                    'name' => $this->l('Yes')
                                ),
                            ),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
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
            MTConfiguration::NAME_ENABLED_CONFIRM_PAGE => Tools::getValue(
                MTConfiguration::NAME_ENABLED_CONFIRM_PAGE,
                (int)MTConfiguration::isEnabledConfirmPage()
            ),
            MTConfiguration::NAME_ENABLED_SUCCESS_PAGE => Tools::getValue(
                MTConfiguration::NAME_ENABLED_SUCCESS_PAGE,
                (int)MTConfiguration::isEnabledSuccessPage()
            ),
        );
    }

    /**
     * Presta 1.5 header hook
     * @deprecated and will be removed later
     * @param $params
     */
    public function hookDisplayHeader($params)
    {
        return $this->fetch(__FILE__, 'header.tpl');
    }

    /**
     * Presta 1.4 header hook
     * @deprecated and will be removed later
     * @param $params
     */
    public function hookHeader($params)
    {
        return $this->fetch(__FILE__, 'header.tpl');
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

        return $this->fetch(__FILE__, 'order-state-info.tpl');
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function hookPayment($params)
    {
        $this->assignTemplateAssets($this->smarty, $params['cart']);

        return $this->fetch(__FILE__, 'payment.tpl');
    }

    /**
     * @param $params
     */
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $status = 'ok';
        if ($params['objOrder']->getCurrentState() == _PS_OS_ERROR_) {
            $status = 'failed';
        }

        $this->smarty->assign('status', $status);

        return $this->fetch(__FILE__, 'payment_return.tpl');
    }

    /**
     * @param $params
     * @return array|void
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        $newOption = new PaymentOption();
        $newOption->setCallToActionText($this->trans('Pay by MisterTango', array(), 'Modules.MTPayment.Shop'))
            ->setAction($this->context->link->getModuleLink($this->name, 'confirm', array(), true))
            ->setAdditionalInformation($this->fetch('module:mtpayment/views/templates/hook/payment_options.tpl'));

        $payment_options = [
            $newOption,
        ];
        return $payment_options;
    }

    /**
     * @param $smarty
     * @param $cart
     */
    public function assignTemplateAssets($smarty, $cart)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/mtpayment.css');

        $currency = new Currency((int)$cart->id_currency);

        $smarty->assign(array(
            'mtpayment_username' => MTConfiguration::getUsername(),
            'mtpayment_enabled_confirm_page' => MTConfiguration::isEnabledConfirmPage(),
            'mtpayment_enabled_success_page' => MTConfiguration::isEnabledSuccessPage(),
            'mtpayment_url_validate_order' => $this->context->link->getModuleLink(
                'mtpayment',
                'validate-order'
            ),
            'mtpayment_url_validate_transaction' => $this->context->link->getModuleLink(
                'mtpayment',
                'validate-transaction'
            ),
            'mtpayment_url_order_states' => $this->context->link->getModuleLink(
                'mtpayment',
                'order-states'
            ),
            'mtpayment_version' => $this->version,
            'mtpayment_path' => $this->_path,
            'enabled_confirm_page' => MTConfiguration::isEnabledConfirmPage(),
            'enabled_success_page' => MTConfiguration::isEnabledSuccessPage(),
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
