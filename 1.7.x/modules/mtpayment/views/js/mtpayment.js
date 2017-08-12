MTPayment = {
    config: {
        autoOpen: null,
        orderId: null,
        username: null,
        callbackUrl: null,
        locale: null
    },
    state: {
        open: false,
        offlinePayment: false,
        success: false
    },
    init: function (config) {
        $.getScript(MTPAYMENT_URL_SCRIPT, function (data, textStatus, jqxhr) {
            MTPayment.config.autoOpen = MTPAYMENT_AUTO_OPEN;
            MTPayment.config.orderId = MTPAYMENT_ORDER_ID;
            MTPayment.config.username = MTPAYMENT_USERNAME;
            MTPayment.config.callbackUrl = MTPAYMENT_CALLBACK_URL;
            MTPayment.config.locale = MTPAYMENT_LANGUAGE;

            MTPayment.load(function () {
                $(document).on('click', '[data-mtpayment-trigger]', function () {
                    MTPayment.open($(this));
                });

                if (MTPayment.config.autoOpen === 1) {
                    MTPayment.open($('[data-mtpayment-trigger]').eq(0));
                }
            });
        });
    },
    load: function (afterLoad) {
        mrTangoCollect.load();

        mrTangoCollect.set.recipient(MTPayment.config.username);

        mrTangoCollect.onOpened = MTPayment.onOpen;
        mrTangoCollect.onClosed = MTPayment.onClose;

        mrTangoCollect.onSuccess = MTPayment.onSuccess;
        mrTangoCollect.onOffLinePayment = MTPayment.onOfflinePayment;

        afterLoad();
    },
    open: function ($target) {
        MTPayment.state.offlinePayment = false;
        MTPayment.state.open = true;

        mrTangoCollect.set.description($target.attr('data-transaction-id'));
        mrTangoCollect.set.payer($target.attr('data-transaction-email'));
        mrTangoCollect.set.amount($target.attr('data-transaction-amount'));
        mrTangoCollect.set.currency($target.attr('data-transaction-currency'));
        mrTangoCollect.set.lang(MTPayment.config.locale);
        if (MTPayment.config.callbackUrl) {
            mrTangoCollect.custom = {'callback': MTPayment.config.callbackUrl};
        }

        mrTangoCollect.submit();
    },
    onOpen: function () {
        MTPayment.state.open = true;
    },
    onOfflinePayment: function (response) {
        mrTangoCollect.onSuccess = function () {};
        MTPayment.state.offlinePayment = true;
    },
    onSuccess: function (response) {
        MTPayment.state.success = true;
    },
    onClose: function () {
        MTPayment.state.open = false;

        if (MTPayment.state.success) {
            window.location.href = MTPAYMENT_URL_ORDER_CONFIRMATION;

            return;
        }

        if (MTPayment.config.autoOpen) {
            window.location.href = MTPAYMENT_URL_ORDER_STATES;
        }
    }
};

$(MTPayment.init);
