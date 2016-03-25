{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='mtpayment'}">{l s='Checkout' mod='mtpayment'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Payment via internet banking (Swedbank, SEB, DNB, etc)' mod='mtpayment'}
{/capture}

<h2>{l s='Order summary' mod='mtpayment'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='mtpayment'}</p>
{else}

<h3>{l s='Internet banking payment (Swedbank, SEB, DNB, etc)' mod='mtpayment'}</h3>
<p>
  {l s='You have chosen to pay via internet banking (Swedbank, SEB, DNB, etc).' mod='mtpayment'}
  <br/><br />
  {l s='Here is a short summary of your order:' mod='mtpayment'}
</p>
<p style="margin-top:20px;">
  - {l s='The total amount of your order is' mod='mtpayment'}
  <span id="amount" class="price">{displayPrice price=$total}</span>
  {if $use_taxes == 1}
      {l s='(tax incl.)' mod='mtpayment'}
    {/if}
</p>
<p>
  {l s='Your order states will be displayed on the next page.' mod='mtpayment'}
  <br /><br />
  <b>{l s='Make payment via internet banking by pressing checkout button below.' mod='mtpayment'}</b>
</p>
<p class="cart_navigation" id="cart_navigation">
  <a href="#"
     class="mtpayment-submit"
     data-language="{$lang_iso|escape:'htmlall':'UTF-8'}"
     data-customer-email="{$customer_email}"
     data-amount="{$amount|escape:'htmlall':'UTF-8'}"
     data-currency="{$cart_currency_iso_code|escape:'htmlall':'UTF-8'}"
     data-transaction="{$transaction|escape:'htmlall':'UTF-8'}">
    {l s='Checkout' mod='mtpayment'} {convertPrice price=$amount}
  </a>
  <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other payment methods' mod='mtpayment'}</a>
</p>
{/if}
