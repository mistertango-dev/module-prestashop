{if version_compare($smarty.const._PS_VERSION_, '1.6', '>')}
<div class="row">
  <div class="col-xs-12">
{/if}

{if $enabled_confirm_page == true}
<p class="payment_module">
  <a href="{$link->getModuleLink('mtpayment', 'confirm')|escape:'html'}" class="mtpayment" title="{l s='Internet banking' mod='mtpayment'}">
    <img src="/modules/mtpayment/views/img/payment.png" />
    {l s='Internet banking' mod='mtpayment'}
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
    {l s='Checkout' mod='mtpayment'} {convertPrice price=$amount}
  </a>
</p>
{include file="modules/mtpayment/views/templates/scripts.tpl"}
{/if}

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>')}
  </div>
</div>
{/if}
