MTPayment = {
    opened: false,
    success: false,
    order: null,
    disallowDifferentPayment: false,
    isOfflinePayment: false,
    transaction: null,
    customerEmail: null,
    amount: null,
    currency: null,
    language: null,
    init: function () {
        mrTangoCollect.load();

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

            mrTangoCollect.submit();

            return false;
        });
    },
    onOpen: function () {
        MTPayment.opened = true;
    },
    onOfflinePayment: function (response) {
        mrTangoCollect.onSuccess = function () {};
        MTPayment.isOfflinePayment = true;
        MTPayment.onSuccess(response);
    },
    onSuccess: function (response) {
        $.ajax({
            type: 'GET',
            async: true,
            dataType: 'json',
            url: MTPayment.order?MTPAYMENT_URL_VALIDATE_TRANSACTION:MTPAYMENT_URL_VALIDATE_ORDER,
            headers: {
                'cache-control': 'no-cache'
            },
            cache: false,
            data: {
                order: MTPayment.order?MTPayment.order:null,
                transaction: MTPayment.transaction,
                websocket: mrTangoCollect.ws_id,
                amount: MTPayment.amount
            },
            success: function(data)
            {
                if (data.success) {
                    $('.jsAllowDifferentPayment').remove();
                    MTPayment.disallowDifferentPayment = true;
                    MTPayment.order = data.order;
                    MTPayment.success = true;

                    if (MTPayment.opened === false) {
                        MTPayment.afterSuccess();
                    }
                }
            }
        });
    },
    onClose: function () {
        MTPayment.opened = false;

        if (MTPayment.success) {
            MTPayment.afterSuccess();
        }
    },
    afterSuccess: function () {
        var operator = '?';

        if (!MTPayment.isOfflinePayment && MTPAYMENT_ENABLED_SUCCESS_PAGE) {
            operator = MTPAYMENT_URL_SUCCESS_PAGE.indexOf('?') === -1?'?':'&';
            window.location.href = MTPAYMENT_URL_SUCCESS_PAGE + operator + 'id_order=' + MTPayment.order;
            return;
        }

        operator = MTPAYMENT_URL_ORDER_STATES.indexOf('?') === -1?'?':'&';
        window.location.href = MTPAYMENT_URL_ORDER_STATES + operator + 'order=' + MTPayment.order;
    }
};

$(MTPayment.init);
