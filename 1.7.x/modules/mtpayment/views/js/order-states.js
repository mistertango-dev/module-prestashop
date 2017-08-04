MTPayment.OrderStates = {
    init: function () {
        setInterval(MTPayment.OrderStates.updateTable, 30000);
    },
    updateTable: function () {
        $.ajax({
            type: 'GET',
            async: true,
            dataType: "json",
            url: MTPAYMENT_URL_ORDER_STATES,
            headers: {
                'cache-control': 'no-cache'
            },
            cache: false,
            data: {
                ajax: true,
                order: MTPAYMENT_ORDER_ID
            },
            success: function(data)
            {
                $('#mtpayment-order-states-table').replaceWith(data.html);

                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            }

        });
    }
};

$(MTPayment.OrderStates.init);
