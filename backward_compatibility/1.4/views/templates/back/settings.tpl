<form action="{$smarty.server.REQUEST_URI|escape:'htmlall'}" method="post">
  <fieldset>
    <legend><img src="{$views_path}/img/logo.gif" alt="" title=""/>{l s='Settings' mod='mtpayment'}</legend>

    <label for="mt_username">{l s='Username' mod='mtpayment'}</label>
    <div class="margin-form">
      <input id="mt_username" type="text" name="{$fields.username.name}" value="{$fields.username.value}"/>
      <sup>*</sup>
    </div>

    <label for="mt_secret_key">{l s='Secret key' mod='mtpayment'}</label>
    <div class="margin-form">
      <input id="mt_secret_key" type="text" name="{$fields.secret_key.name}" value="{$fields.secret_key.value}"/>
      <sup>*</sup>
    </div>

    <label for="mt_enable_confirm_page">{l s='Enable standard mode' mod='mtpayment'}</label>
    <div class="margin-form">
      <select id="mt_enable_confirm_page" name="{$fields.enable_confirm_page.name}">
        <option {if not $fields.enable_confirm_page.value == 0}selected{/if} value="0">No</option>
        <option {if $fields.enable_confirm_page.value == 1}selected{/if} value="1">Yes</option>
      </select>
      <sup>*</sup>
    </div>

    <label for="mt_enable_success_page">{l s='Enable success page' mod='mtpayment'}</label>
    <div class="margin-form">
      <select id="mt_enable_success_page" name="{$fields.enable_success_page.name}">
        <option {if $fields.enable_success_page.value == 0}selected{/if} value="0">No</option>
        <option {if $fields.enable_success_page.value == 1}selected{/if} value="1">Yes</option>
      </select>
      <sup>*</sup>
    </div>

    <div class="margin-form">
      <input type="submit" name="btnSubmit" value="{l s='save' mod='mtpayment'}" class="button"/>
    </div>
  </fieldset>
</form>
