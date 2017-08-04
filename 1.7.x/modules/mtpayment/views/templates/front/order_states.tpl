{extends file='page.tpl'}

{block name='page_content_container'}
    <section class="page-content card card-block">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    {l s='Payment information' mod='mtpayment'}
                </h1>
                {include file="modules/mtpayment/views/templates/front/_partials/order_states_table.tpl"}
            </div>
        </div>
    </section>
    <script type="text/javascript">
        var MTPAYMENT_LANGUAGE = "{$language['iso_code']|escape:'html':'UTF-8'}";
        var MTPAYMENT_USERNAME = "{$mtpayment_username}";
        var MTPAYMENT_CALLBACK_URL = "{$mtpayment_callback_url}";
        var MTPAYMENT_URL_ORDER_CONFIRMATION = "{$url_order_confirmation}";
        var MTPAYMENT_URL_ORDER_STATES = "{$url_order_states}";
        var MTPAYMENT_ORDER_ID = "{$order->id}";
        var MTPAYMENT_URL_SCRIPT = "https://payment.mistertango.com/resources/scripts/mt.collect.js?v={$smarty.now|escape:'htmlall':'UTF-8'}";
    </script>
    <script type="text/javascript" src="{$mtpayment_path}/views/js/mtpayment.js"></script>
{/block}
