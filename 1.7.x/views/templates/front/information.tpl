{capture name=path}
<a href="{$link->getPageLink('order', true, NULL, " step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='mistertango'}">{l s='Checkout' mod='mistertango'}</a>
<span class="navigation-pipe">{$navigationPipe|escape:'htmlall':'UTF-8'}</span>{l s='MisterTango payment' mod='mistertango'}
{/capture}

<script type="text/javascript">
  var mrTangoIdOrder = "{$order->id|escape:'htmlall':'UTF-8'}";
</script>

<h1 class="page-heading">
  {l s='Payment information' mod='mistertango'}
</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="table_block">
  {include file="./table_order_states.tpl"}
</div>

{include file="modules/mtpayment/views/templates/scripts.tpl"}
