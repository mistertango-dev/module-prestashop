{extends file='page.tpl'}

{block name='page_content_container'}
    <section class="page-content card card-block">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    {l s='Payment information' mod='mtpayment'}
                </h1>
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
                {include file="modules/mtpayment/views/templates/front/_partials/order_states_table.tpl"}
            </div>
        </div>
    </section>
    <script type="text/javascript">
        var MTPAYMENT_AUTO_OPEN = {$auto_open|intval};
        var MTPAYMENT_ORDER_ID = "{$order->id}";
        var MTPAYMENT_USERNAME = "{$mtpayment_username}";
        var MTPAYMENT_CALLBACK_URL = "{$mtpayment_callback_url}";
        var MTPAYMENT_LANGUAGE = "{$language['iso_code']|escape:'html':'UTF-8'}";
        var MTPAYMENT_URL_ORDER_CONFIRMATION = "{$url_order_confirmation}";
        var MTPAYMENT_URL_ORDER_STATES = "{$url_order_states}";
        var MTPAYMENT_URL_SCRIPT = "https://payment.mistertango.com/resources/scripts/mt.collect.js?v={$smarty.now|escape:'htmlall':'UTF-8'}";
    </script>
    <script type="text/javascript" src="{$mtpayment_path}views/js/mtpayment.js"></script>
    <script type="text/javascript" src="{$mtpayment_path}views/js/order-states.js"></script>
{/block}
