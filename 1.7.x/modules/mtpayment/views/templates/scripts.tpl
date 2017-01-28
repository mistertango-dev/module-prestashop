<script type="text/javascript">
  var MTPAYMENT_USERNAME = "{$mtpayment_username}";
  var MTPAYMENT_ENABLED_CONFIRM_PAGE = {$mtpayment_enabled_confirm_page};
  var MTPAYMENT_ENABLED_SUCCESS_PAGE = {$mtpayment_enabled_success_page};
  var MTPAYMENT_URL_VALIDATE_ORDER = "{$mtpayment_url_validate_order}";
  var MTPAYMENT_URL_VALIDATE_TRANSACTION = "{$mtpayment_url_validate_transaction}";
  var MTPAYMENT_URL_ORDER_STATES = "{$mtpayment_url_order_states}";
  var MTPAYMENT_URL_SCRIPT = "https://payment.mistertango.com/resources/scripts/mt.collect.js?v={$smarty.now|escape:'htmlall':'UTF-8'}";
</script>
<script type="text/javascript" src="{$mtpayment_path}/views/js/mtpayment.js"></script>
