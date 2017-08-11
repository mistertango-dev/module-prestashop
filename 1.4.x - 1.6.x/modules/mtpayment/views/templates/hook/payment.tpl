{if version_compare($smarty.const._PS_VERSION_, '1.6', '>')}
<div class="row">
  <div class="col-xs-12">
{/if}

<p class="payment_module">
  <a href="{$link->getModuleLink('mtpayment', 'validation')|escape:'html'}" class="bankwire" title="{l s='Internet banking' mod='mtpayment'}">
    {l s='Internet banking' mod='mtpayment'}
  </a>
</p>

{if version_compare($smarty.const._PS_VERSION_, '1.6', '>')}
  </div>
</div>
{/if}
