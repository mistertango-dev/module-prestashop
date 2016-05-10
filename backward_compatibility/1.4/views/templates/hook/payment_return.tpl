{if $status == 'ok'}
<p>
  {l s='Your order on' mod='mtpayment'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='mtpayment'}
  <br/><br/>
  {l s='If you have questions, comments or concerns, please contact our' mod='mtpayment'}
  <a href="{$link->getPageLink('contact', true)|escape:'html'}">
    {l s='expert customer support team' mod='mtpayment'}
  </a>.
</p>
{else}
<p class="warning">
  {l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='mtpayment'}
  <a href="{$link->getPageLink('contact', true)|escape:'html'}">
    {l s='expert customer support team' mod='mtpayment'}
  </a>.
</p>
{/if}
