{if $enbaled_confirm_page == true}
	<p class="payment_module">
		<a href="{$confirm_link}" class="mtpayment" title="{l s='Internet banking' mod='mtpayment'}">
			<img src="/modules/mtpayment/backward_compatibility/1.4/views/img/payment.png" />
			Internet banking
		</a>
	</p>
{else}
	<p id="mtpayment" class="payment_module">
		<a href="#"
		   class="mtpayment-submit"
		   data-language="{$lang_iso|escape:'htmlall':'UTF-8'}"
		   data-customer-email="{$customer_email}"
		   data-amount="{$amount|escape:'htmlall':'UTF-8'}"
		   data-currency="{$cart_currency_iso_code|escape:'htmlall':'UTF-8'}"
		   data-transaction="{$transaction|escape:'htmlall':'UTF-8'}">
			<img src="/modules/mtpayment/backward_compatibility/1.4/views/img/payment.png">
			{l s='Pay with MisterTango' mod='mtpayment'}
		</a>
	</p>
  {include file="modules/mtpayment/views/templates/scripts.tpl"}
{/if}
