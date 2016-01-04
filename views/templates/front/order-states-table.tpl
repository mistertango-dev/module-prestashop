<table id="mtpayment-order-states-table" class="table table-bordered{if version_compare($smarty.const._PS_VERSION_, '1.6', '<')} std{/if}">
  <thead>
  <tr>
    <th width="20%">{l s='Date' mod='mtpayment'}</th>
    <th width="80%">{l s='Status' mod='mtpayment'}</th>
  </tr>
  </thead>
  <tbody>
  {foreach from=$history item=row key=key name=history}
  {if ($key == 0)}
  <tr class="{if $smarty.foreach.history.first}first_item{/if}{if $smarty.foreach.history.last} last_item{/if}{if $smarty.foreach.history.index % 2} alternate_item{else} item{/if}">
    <td class="step-by-step-date">
      {dateFormat date=$row['date_add'] full=true}
    </td>
    <td>
      <p>
        <span style="{if isset($row.color) && $row.color}background-color:{$row.color|escape:'html':'UTF-8'}; border-color:{$row.color|escape:'html':'UTF-8'};{/if}" class="label{if isset($row.color) && Tools::getBrightness($row.color) > 128} dark{/if}">
          {$row.ostate_name|escape:'html':'UTF-8'}
        </span>
      </p>
      {if $row['id_order_state'] == $id_order_state_pending && $allow_different_payment}
      <p class="jsAllowDifferentPayment">
        {l s='Check your email, we sent you an invoice. If you wish to use other methods for payment' mod='mtpayment'} -
        <a href="#"
           class="mtpayment-submit"
           data-ws-id="{$transaction.id_websocket|escape:'htmlall':'UTF-8'}"
           data-language-code="{$lang_iso|escape:'htmlall':'UTF-8'}"
           data-customer-email="{$customer_email}"
           data-id-order="{$order->id|escape:'htmlall':'UTF-8'}"
           data-amount="{$amount|escape:'htmlall':'UTF-8'}"
           data-currency="{$cart_currency_iso_code|escape:'htmlall':'UTF-8'}"
           data-id-transaction="{$transaction|escape:'htmlall':'UTF-8'}">
          {l s='click here' mod='mtpayment'}
        </a>
      </p>
      {/if}
    </td>
  </tr>
  {else}
  <tr class="{if $smarty.foreach.history.first}first_item{elseif $smarty.foreach.history.last}last_item{/if} {if $smarty.foreach.history.index % 2}alternate_item{else}item{/if}">
    <td class="step-by-step-date">
      {dateFormat date=$row['date_add'] full=true}
    </td>
    <td>
      <p>
        <span style="{if isset($row.color) && $row.color}background-color:{$row.color|escape:'html':'UTF-8'}; border-color:{$row.color|escape:'html':'UTF-8'};{/if}" class="label{if isset($row.color) && Tools::getBrightness($row.color) > 128} dark{/if}">
          {$row.ostate_name|escape:'html':'UTF-8'}
        </span>
      </p>
      {if $row['id_order_state'] == $id_order_state_pending && $allow_different_payment}
      <p class="jsAllowDifferentPayment">
        {l s='Check your email, we sent you an invoice. If you wish to use other methods for payment' mod='mtpayment'} -
        <a href="#" class="mtpayment-submit" data-ws-id="{$transaction.id_websocket|escape:'htmlall':'UTF-8'}">
          {l s='click here' mod='mtpayment'}
        </a>
      </p>
      {/if}
    </td>
  </tr>
  {/if}
  {/foreach}
  </tbody>
</table>
