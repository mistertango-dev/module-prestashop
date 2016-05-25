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
  <table id="mtpayment-order-states-table" class="table table-bordered{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')} std{/if}">
    <thead>
    <tr>
      <th width="20%">{l s='Date' mod='mtpayment'}</th>
      <th width="80%">{l s='Status' mod='mtpayment'}</th>
    </tr>
    </thead>
    <tbody>
    <tr class="{if $smarty.foreach.history.first}first_item{elseif $smarty.foreach.history.last}last_item{/if} {if $smarty.foreach.history.index % 2}alternate_item{else}item{/if}">
      <td class="step-by-step-date">
        {$smarty.now|date_format:'%Y-%m-%d'}
      </td>
      <td>
        <p>
          {l s='If you wish to use our method for payment' mod='mtpayment'} -
          <a href="#"
             class="mtpayment-submit"
             data-language="{$lang_iso|escape:'htmlall':'UTF-8'}"
             data-customer-email="{$customer_email}"
             data-amount="{$amount|escape:'htmlall':'UTF-8'}"
             data-currency="{$cart_currency_iso_code|escape:'htmlall':'UTF-8'}"
             data-transaction="{$transaction|escape:'htmlall':'UTF-8'}">
            {l s='click here' mod='mtpayment'}
          </a>
        </p>
      </td>
    </tr>
    </tbody>
  </table>
</div>

{include file="modules/mtpayment/views/templates/scripts.tpl"}
<script type="text/javascript">
  $(window).load(function () { $('.mtpayment-submit').eq(0).trigger('click'); })
</script>
