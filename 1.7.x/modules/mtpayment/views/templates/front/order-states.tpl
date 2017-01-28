{extends file='page.tpl'}

{block name='page_content_container'}
<section class="page-content card card-block">
    <div class="row">
        <div class="col-xs-12">
            <h1>
                {l s='Payment information' mod='mtpayment'}
            </h1>
            {include file="modules/mtpayment/views/templates/front/order-states-table.tpl"}
        </div>
    </div>
</section>

<script type="text/javascript">
    var MTPAYMENT_ORDER_ID = "{$order->id|escape:'htmlall':'UTF-8'}";
</script>
{include file="modules/mtpayment/views/templates/scripts.tpl"}

{/block}
