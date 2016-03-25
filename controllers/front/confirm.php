<?php

require_once(dirname(__FILE__) . '/../../mtpayment.php');

/**
 * Class MTPaymentConfirmModuleFrontController
 */
class MTPaymentConfirmModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$cart = $this->context->cart;

        MTPayment::getInstance()->assignTemplateAssets($this->context->smarty, $cart);

		$this->context->smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => MTPayment::getInstance()->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'this_path' => MTPayment::getInstance()->getPathUri(),
			'this_path_bw' => MTPayment::getInstance()->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.MTPayment::getInstance()->name.'/'
		));

		$this->setTemplate('confirm.tpl');
	}
}
