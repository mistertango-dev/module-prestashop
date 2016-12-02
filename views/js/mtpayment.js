MTPayment = {
    isOpen: false,
    success: false,
    order: null,
    disallowDifferentPayment: false,
    isOfflinePayment: false,
    urlSuccessPage: null,
    transaction: null,
    customerEmail: null,
    amount: null,
    currency: null,
    language: null,
    init: function () {
        mrTangoCollect.set.recipient(MTPAYMENT_USERNAME);

        mrTangoCollect.onOpened = MTPayment.onOpen;
        mrTangoCollect.onClosed = MTPayment.onClose;

        mrTangoCollect.onSuccess = MTPayment.onSuccess;
        mrTangoCollect.onOffLinePayment = MTPayment.onOfflinePayment;

        MTPayment.initButtonPay();
    },
    initButtonPay: function () {
        $(document).on('click', '.mtpayment-submit', function (e) {
            if (e.isDefaultPrevented()) {
                return;
            }

            if (typeof $(this).data('ws-id') != 'undefined') {
                mrTangoCollect.ws_id = $(this).data('websocket');
            }

            MTPayment.order = null;

            if (typeof $(this).data('id-order') != 'undefined') {
                MTPayment.order = $(this).data('order');
            }

            MTPayment.transaction = $(this).data('transaction');
            MTPayment.customerEmail = $(this).data('customer-email');
            MTPayment.amount = $(this).data('amount');
            MTPayment.currency = $(this).data('currency');
            MTPayment.language = $(this).data('language');

            mrTangoCollect.set.payer(MTPayment.customerEmail);
            mrTangoCollect.set.amount(MTPayment.amount);
            mrTangoCollect.set.currency(MTPayment.currency);
            mrTangoCollect.set.description(MTPayment.transaction);
            mrTangoCollect.set.lang(MTPayment.language);

            MTPayment.isOpen = true;
            mrTangoCollect.submit();

            return false;
        });
    },
    onOpen: function () {
        MTPayment.isOpen = true;
    },
    onOfflinePayment: function (response) {
        mrTangoCollect.onSuccess = function () {
        };
        MTPayment.isOfflinePayment = true;
        MTPayment.onSuccess(response);
    },
    onSuccess: function (response) {
        $.ajax({
            type: 'GET',
            async: true,
            dataType: 'json',
            url: MTPayment.order ? MTPAYMENT_URL_VALIDATE_TRANSACTION : MTPAYMENT_URL_VALIDATE_ORDER,
            headers: {
                'cache-control': 'no-cache'
            },
            cache: false,
            data: {
                order: MTPayment.order ? MTPayment.order : null,
                transaction: MTPayment.transaction,
                websocket: mrTangoCollect.ws_id,
                amount: MTPayment.amount
            },
            success: function (data) {
                if (data.success) {
                    $('.jsAllowDifferentPayment').remove();
                    MTPayment.disallowDifferentPayment = true;
                    MTPayment.order = data.order;
                    MTPayment.success = true;
                    MTPayment.urlSuccessPage = data.url_success_page;

                    if (MTPayment.isOpen === false) {
                        MTPayment.afterSuccess();
                    }
                }
            }
        });
    },
    onClose: function () {
        MTPayment.isOpen = false;

        if (MTPayment.success) {
            MTPayment.afterSuccess();
        }
    },
    afterSuccess: function () {
        var url = MTPAYMENT_URL_ORDER_STATES;
        if (MTPAYMENT_ENABLED_SUCCESS_PAGE && !MTPayment.isOfflinePayment) {
            url = MTPayment.urlSuccessPage;
        }

        var operator = url.indexOf('?') === -1 ? '?' : '&';
        window.location.href = url + operator + 'id_order=' + MTPayment.order;
    }
};

document.addEventListener(
    "DOMContentLoaded",
    function () {
        $.getScript(MTPAYMENT_URL_SCRIPT, function (data, textStatus, jqxhr) {
            MTPayment.init();
        });
    },
    false
);




