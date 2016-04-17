<?php
/**
 * Route to controllers
 */

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');

class ModuleFrontController extends FrontController
{
    public $module;
    public $context;
    private $template;

    public function initContent() {}

    public function __construct()
    {
        $this->controller_type = 'modulefront';

        $this->module = Module::getInstanceByName('mtpayment');
        if (!$this->module->active)
            Tools::redirect('index');
        $this->page_name = 'module-'.$this->module->name.'-'.strtolower(str_replace('-','',$_GET['controller']));
        $mtPayment = new MTPayment();
        $this->context = $mtPayment->context;
        parent::__construct();
    }

    /**
     * Assign module template
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        if(file_exists(_PS_MODULE_DIR_ . $this->module->name.'/views/templates/front/'.$template)) {
            $this->template = _PS_MODULE_DIR_ . $this->module->name.'/views/templates/front/'.$template;
        }
    }

    /**
     * Display module template
     */
    function display()
    {
        include('../../header.php');
        echo $this->context->smarty->fetch($this->template);
        include('../../footer.php');
    }

    /**
     * Get path to front office templates for the module
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return _PS_MODULE_DIR_.$this->module->name.'/views/templates/front/';
    }
}

function uc_words($string)
{
    $string = explode( "-", $string );
    foreach( $string as &$v ) {
        $v = ucfirst( $v );
    }
    return implode( "", $string );
}

if(isset($_GET['controller'])) {
    $controllerName = uc_words($_GET['controller']);
    $controllerClass = 'MTPayment'.$controllerName.'ModuleFrontController';
    $controllerFile = strtolower($controllerName) . '.php';

    if(file_exists(dirname(__FILE__).'/controllers/front/' . $controllerFile)) {

        require_once('controllers/front/' . $controllerFile);

        if(class_exists($controllerClass)) {
            $object = new $controllerClass();
            $object->initContent();
            $object->display();
        }
    }
}