{extends file='page.tpl'}

{block name='page_content_container'}
<section id="content" class="page-content page-order-confirmation card">
    <div class="card-block">
        <div class="row">
            <div class="col-xs-12">
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
                                   data-language=""
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
        </div>
    </div>
</section>

{include file="modules/mtpayment/views/templates/scripts.tpl"}
<script type="text/javascript">
    $(window).load(function () {
        $('.mtpayment-submit').eq(0).trigger('click');
    })
</script>

{hook h='displayOrderConfirmation1'}

<section id="content-hook-order-confirmation-footer">
    {hook h='displayOrderConfirmation2'}
</section>
{/block}


