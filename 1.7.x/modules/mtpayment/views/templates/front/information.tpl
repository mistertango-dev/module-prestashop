{extends file='page.tpl'}

{block name='page_content_container'}
<script type="text/javascript">
    var mrTangoIdOrder = "{$order->id|escape:'htmlall':'UTF-8'}";
</script>
<section class="page-content card card-block">
  <div class="row">
    <div class="col-xs-12">
      <h1>
        {l s='Payment information' mod='mtpayment'}
      </h1>
      {include file="./table_order_states.tpl"}
    </div>
  </div>
</section>
{include file="modules/mtpayment/views/templates/scripts.tpl"}
{/block}
