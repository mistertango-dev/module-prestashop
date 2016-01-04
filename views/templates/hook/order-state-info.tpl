{if $allow_different_payment}
<p class="jsAllowDifferentPayment">
  {l s='Check your email, we sent you an invoice. If you wish to use other methods for payment' mod='mtpayment'} -
  <a href="#"
     class="mtpayment-submit"
     data-websocket="{$transaction.id_websocket|escape:'htmlall':'UTF-8'}"
     data-language="{$lang_iso|escape:'htmlall':'UTF-8'}"
     data-customer-email="{$customer_email}"
     data-order="{$order->id|escape:'htmlall':'UTF-8'}"
     data-amount="{$amount|escape:'htmlall':'UTF-8'}"
     data-currency="{$cart_currency_iso_code|escape:'htmlall':'UTF-8'}"
     data-transaction="{$transaction|escape:'htmlall':'UTF-8'}">
    {l s='click here' mod='mtpayment'}
  </a>
</p>
{/if}
