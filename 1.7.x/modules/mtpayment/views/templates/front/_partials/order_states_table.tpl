<table id="mtpayment-order-states-table" class="table table-bordered">
  <thead>
  <tr>
    <th width="20%">{l s='ID' mod='mtpayment'}</th>
    <th width="20%">{l s='Date' mod='mtpayment'}</th>
    <th width="60%">{l s='Status' mod='mtpayment'}</th>
  </tr>
  </thead>
  <tbody>
  {foreach from=$history item=row key=key name=history}
    <tr>
      <td>
          {$row['id_order']}
      </td>
      <td>
          {dateFormat date=$row['date_add'] full=true}
      </td>
      <td>
        <p>
          <span style="{if isset($row.color) && $row.color}background-color:{$row.color|escape:'html':'UTF-8'}; border-color:{$row.color|escape:'html':'UTF-8'};{/if}" class="label{if isset($row.color) && Tools::getBrightness($row.color) > 128} dark{/if}">
            {$row.ostate_name|escape:'html':'UTF-8'}
          </span>
        </p>
      </td>
    </tr>
  {/foreach}
  </tbody>
</table>
