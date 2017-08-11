MTPayment = {
    isOpen: false,
    success: false,
    order: null,
    isOfflinePayment: false,
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

            mrTangoCollect.set.description($(this).data('transaction-id'));
            mrTangoCollect.set.payer($(this).data('transaction-email'));
            mrTangoCollect.set.amount($(this).data('transaction-amount'));
            mrTangoCollect.set.currency($(this).data('transaction-currency'));
            mrTangoCollect.set.lang(MTPAYMENT_LANGUAGE);
            if (MTPAYMENT_CALLBACK_URL) {
                mrTangoCollect.custom = {'callback': MTPAYMENT_CALLBACK_URL};
            }

            MTPayment.isOpen = true;
            mrTangoCollect.submit();

            return false;
        });
    },
    onOpen: function () {
        MTPayment.isOpen = true;
    },
    onOfflinePayment: function (response) {
        mrTangoCollect.onSuccess = function () {};
        MTPayment.isOfflinePayment = true;
    },
    onSuccess: function (response) {
        MTPayment.success = true;

        if (MTPayment.isOpen === false) {
            window.location.href = MTPAYMENT_URL_ORDER_CONFIRMATION;
        }
    },
    onClose: function () {
        MTPayment.isOpen = false;

        if (MTPayment.success) {
            window.location.href = MTPAYMENT_URL_ORDER_CONFIRMATION;
        }
    }
};

document.addEventListener(
    'DOMContentLoaded',
    function () {
        $.getScript(MTPAYMENT_URL_SCRIPT, function (data, textStatus, jqxhr) {
            MTPayment.init();
        });
    },
    false
);
