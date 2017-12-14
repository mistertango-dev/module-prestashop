<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'mtpayment/mtconfiguration.php');
require_once(_PS_MODULE_DIR_.'mtpayment/mtorders.php');

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
        $this->version = '1.3.5';
        $this->author = 'MisterTango';
        $this->is_eu_compatible = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->page = basename(__FILE__, '.php');
        $this->displayName = $this->l('MisterTango Payment');
        $this->description = $this->l('Add MisterTango payment gateway to your shop.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete module with all logs & details?');

        /* Backward compatibility */
        if (_PS_VERSION_ < '1.5') {
            require _PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php';
            require_once(_PS_MODULE_DIR_.'mtpayment/backward_compatibility/1.4/mtpayment_1.4.php');
        }

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
        );

        if (
            !parent::install() ||
            !$this->registerHooks($hooks) ||
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
        foreach ($hooks AS $hook) {
            if (!$this->registerHook($hook)) {
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
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $this->postProcess();

        if (_PS_VERSION_ < '1.5') {
            $this->_html = MtPayment_1_4::renderForm();
        } else {
            $this->_html = $this->renderForm();
        }

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
            MTConfiguration::updateCallbackUrl(Tools::getValue(MTConfiguration::NAME_CALLBACK_URL));
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
                        'type' => 'text',
                        'label' => $this->l('Callback URL'),
                        'name' => MTConfiguration::NAME_CALLBACK_URL,
                        'required' => false,
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
            .'&configure='
            .$this->name.'&tab_module='
            .$this->tab.'&module_name='
            .$this->name;
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
            MTConfiguration::NAME_CALLBACK_URL => Tools::getValue(
                MTConfiguration::NAME_CALLBACK_URL,
                MTConfiguration::getCallbackUrl()
            ),
        );
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    public function hookPayment($params)
    {
        if (_PS_VERSION_ < '1.5') {
            return MtPayment_1_4::renderPayButton();
        } else {
            return $this->display(__FILE__, 'payment.tpl');
        }
    }

    /**
     * @param $params
     * @return mixed|void
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

        if (_PS_VERSION_ < '1.5') {
            return $this->display(__FILE__, 'backward_compatibility/1.4/views/templates/hook/payment_return.tpl');
        } else {
            return $this->display(__FILE__, 'payment_return.tpl');
        }
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
