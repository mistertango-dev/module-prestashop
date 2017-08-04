<table id="mtpayment-order-states-table" class="table table-bordered">
  <thead>
  <tr>
    <th width="20%">{l s='Date' mod='mtpayment'}</th>
    <th width="80%">{l s='Status' mod='mtpayment'}</th>
  </tr>
  </thead>
  <tbody>
  {foreach from=$history item=row key=key name=history}
  {if ($key == 0)}
  <tr>
    <td>
      {dateFormat date=$row['date_add'] full=true}
    </td>
    <td>
      <p>
        <span style="{if isset($row.color) && $row.color}background-color:{$row.color|escape:'html':'UTF-8'}; border-color:{$row.color|escape:'html':'UTF-8'};{/if}" class="label{if isset($row.color) && Tools::getBrightness($row.color) > 128} dark{/if}">
          {$row.ostate_name|escape:'html':'UTF-8'}
        </span>
      </p>
      <p>
        {l s='Check your email, we sent you an invoice. If you wish to use other methods for payment' mod='mtpayment'} -
        <a href="#"
           class="mtpayment-submit"
           data-transaction-email="{$customer->email}"
           data-transaction-amount="{$order->total_paid|escape:'htmlall':'UTF-8'}"
           data-transaction-currency="{$currency->iso_code|escape:'htmlall':'UTF-8'}"
           data-transaction-id="{$transaction_id|escape:'htmlall':'UTF-8'}"
        >
          {l s='click here' mod='mtpayment'}
        </a>
      </p>
    </td>
  </tr>
  {else}
  <tr>
    <td>
      {dateFormat date=$row['date_add'] full=true}
    </td>
    <td>
      <p>
        <span style="{if isset($row.color) && $row.color}background-color:{$row.color|escape:'html':'UTF-8'}; border-color:{$row.color|escape:'html':'UTF-8'};{/if}" class="label{if isset($row.color) && Tools::getBrightness($row.color) > 128} dark{/if}">
          {$row.ostate_name|escape:'html':'UTF-8'}
        </span>
      </p>
      <p>
        {l s='Check your email, we sent you an invoice. If you wish to use other methods for payment' mod='mtpayment'} -
        <a href="#"
           class="mtpayment-submit"
           data-transaction-email="{$customer->email}"
           data-transaction-amount="{$order->total_paid|escape:'htmlall':'UTF-8'}"
           data-transaction-currency="{$currency->iso_code|escape:'htmlall':'UTF-8'}"
           data-transaction-id="{$transaction_id|escape:'htmlall':'UTF-8'}"
        >
          {l s='click here' mod='mtpayment'}
        </a>
      </p>
    </td>
  </tr>
  {/if}
  {/foreach}
  </tbody>
</table>
