<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

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
        $this->author = 'MisterTango';
        $this->version = '1.3.0';
        $this->need_instance = 1;

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->controllers = array('confirm', 'orderstates');
        $this->is_eu_compatible = 1;
        $this->currencies = false;

        $this->bootstrap = true;
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
            'paymentReturn',
            'paymentOptions',
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

        return $this->renderForm();
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
                    'title' => $this->getTranslator()->trans('Settings', array(), 'Admin.Global'),
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
     * @return mixed|void
     */
    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        $status = 'ok';
        if ($params['order']->getCurrentState() == _PS_OS_ERROR_) {
            $status = 'failed';
        }

        $this->smarty->assign('status', $status);

        return $this->fetch('module:mtpayment/views/templates/hook/payment_return.tpl');
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
        $newOption
            ->setModuleName($this->name)
            ->setCallToActionText($this->trans('Pay by MisterTango', array(), 'Modules.MTPayment.Shop'))
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
            ->setAdditionalInformation($this->fetch('module:mtpayment/views/templates/hook/payment_options.tpl'));
        $payment_options = [
            $newOption,
        ];

        return $payment_options;
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
