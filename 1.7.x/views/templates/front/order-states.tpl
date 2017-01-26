{extends file='page.tpl'}

{block name='page_content_container'}
<section id="content" class="page-content page-order-confirmation card">
    <div class="card-block">
        <div class="row">
            <div class="col-xs-12">
                {include file="modules/mtpayment/views/templates/front/order-states-table.tpl"}
            </div>
        </div>
    </div>
</section>

{include file="modules/mtpayment/views/templates/scripts.tpl"}
<script type="text/javascript">
    var MTPAYMENT_ORDER_ID = "{$order->id|escape:'htmlall':'UTF-8'}";
</script>

{/block}
