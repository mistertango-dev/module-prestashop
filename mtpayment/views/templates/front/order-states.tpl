{capture name=path}
<a href="{$link->getPageLink('order', true, NULL, " step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='mtpayment'}">
  {l s='Checkout' mod='mtpayment'}
</a>
<span class="navigation-pipe">
  {$navigationPipe|escape:'htmlall':'UTF-8'}
</span>
{l s='MisterTango payment' mod='mtpayment'}
{/capture}

<h1 class="page-heading">
  {l s='Payment information' mod='mtpayment'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="table_block">
  {include file="./order-states-table.tpl"}
</div>

<script type="text/javascript">
  var MTPAYMENT_ORDER_ID = "{$order->id|escape:'htmlall':'UTF-8'}";
</script>
