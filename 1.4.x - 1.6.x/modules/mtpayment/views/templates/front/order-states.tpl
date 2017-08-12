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
    <p>
        {l s='Check your email, we sent you an invoice. If you wish to use other methods for payment' mod='mtpayment'} -
        <a href="#"
           data-mtpayment-trigger=""
           data-transaction-email="{$customer->email}"
           data-transaction-amount="{$order->total_paid|escape:'htmlall':'UTF-8'}"
           data-transaction-currency="{$currency->iso_code|escape:'htmlall':'UTF-8'}"
           data-transaction-id="{$transaction_id|escape:'htmlall':'UTF-8'}"
        >
            {l s='click here' mod='mtpayment'}
        </a>
    </p>
    {include file="./order-states-table.tpl"}
</div>

<script type="text/javascript">
    var MTPAYMENT_AUTO_OPEN = {$auto_open|intval};
    var MTPAYMENT_ORDER_ID = "{$order->id}";
    var MTPAYMENT_USERNAME = "{$mtpayment_username}";
    var MTPAYMENT_CALLBACK_URL = "{$mtpayment_callback_url}";
    var MTPAYMENT_LANGUAGE = "{$lang_iso}";
    var MTPAYMENT_URL_ORDER_CONFIRMATION = "{$url_order_confirmation}";
    var MTPAYMENT_URL_ORDER_STATES = "{$url_order_states}";
    var MTPAYMENT_URL_SCRIPT = "https://payment.mistertango.com/resources/scripts/mt.collect.js?v={$smarty.now|escape:'htmlall':'UTF-8'}";
</script>
