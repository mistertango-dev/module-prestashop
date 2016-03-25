{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
<p id="mtpayment" class="payment_module">
  {if $mtpayment_enabled_confirm_page}
  <a href="{$link->getModuleLink('mtpayment', 'confirm')|escape:'html'}" title="{l s='Pay via internet banking (Swedbank, SEB, DNB, etc)' mod='mtpayment'}">
    {l s='Pay via internet banking (Swedbank, SEB, DNB, etc)' mod='mtpayment'}
  </a>
  {else}
  <a href="#"
     class="mtpayment-submit"
     data-language="{$lang_iso|escape:'htmlall':'UTF-8'}"
     data-customer-email="{$customer_email}"
     data-amount="{$amount|escape:'htmlall':'UTF-8'}"
     data-currency="{$cart_currency_iso_code|escape:'htmlall':'UTF-8'}"
     data-transaction="{$transaction|escape:'htmlall':'UTF-8'}">
    {l s='Checkout' mod='mtpayment'} {convertPrice price=$amount}
  </a>
  {/if}
</p>
{else}
<div class="row">
  <div class="col-xs-12">
    <p id="mtpayment" class="payment_module">
      {if $mtpayment_enabled_confirm_page}
      <a href="{$link->getModuleLink('mtpayment', 'confirm')|escape:'html'}" title="{l s='Pay via internet banking (Swedbank, SEB, DNB, etc)' mod='mtpayment'}">
        Pay via internet banking (Swedbank, SEB, DNB, etc)
      </a>
      {else}
      <a href="#"
         class="mtpayment-submit"
         data-language="{$lang_iso|escape:'htmlall':'UTF-8'}"
         data-customer-email="{$customer_email}"
         data-amount="{$amount|escape:'htmlall':'UTF-8'}"
         data-currency="{$cart_currency_iso_code|escape:'htmlall':'UTF-8'}"
         data-transaction="{$transaction|escape:'htmlall':'UTF-8'}">
        {l s='Checkout' mod='mtpayment'} {convertPrice price=$amount}
      </a>
      {/if}
    </p>
  </div>
</div>
{/if}
