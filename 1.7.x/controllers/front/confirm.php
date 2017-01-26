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

		$this->setTemplate('module:mtpayment/views/templates/front/confirm.tpl');
	}
}
