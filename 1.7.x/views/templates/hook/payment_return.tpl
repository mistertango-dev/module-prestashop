{if $status == 'ok'}
<p>
  {l s='Your order on %s is complete.' sprintf=$shop_name mod='mtpayment'}
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
