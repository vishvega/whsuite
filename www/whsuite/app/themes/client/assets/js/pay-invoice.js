$(function() {
    $('#account').change(function(e){

        var gateway_val = $(this).val();

        if(gateway_val == 'creditcard') {

            $('#creditcard_dropdown').show();
            $('#ach_dropdown').hide();
        } else if(gateway_val == 'ach') {

            $('#ach_dropdown').show();
            $('#creditcard_dropdown').hide();
        } else {

            $('#creditcard_dropdown').hide();
            $('#ach_dropdown').hide();
        }

        // check for a gateway, we can then check if they want to serve
        // a different payment button (for the likes of stripe)
        if (gateway_val.substr(0, 7) === 'gateway') {

            var gateway_val = gateway_val.substr(8);

            // pass the gateway to a url to check if it, itself has a url for
            // swapping out the payment button
            var check_url = $(this).data('route-check-url');
            var invoice_id = $(this).data('invoice-id');
            check_url = check_url.replace('GATEWAY', gateway_val);
            check_url = check_url.replace('INVOICEID', invoice_id);

            $.ajax({
                'url': check_url,
                'cache': false,
                success: function(data, textStatus, jqXHR) {

                    if (data.result && data.gateway_url != '') {

                        var pay_button = data.default_btn;

                        // now try and get the gateways button
                        $.ajax({
                            'url': data.gateway_url,
                            'cache': false,
                            success: function(button, textStatus, jqXHR) {

                                if (button.result && button.html != '') {

                                    pay_button = button.html;
                                }
                            },
                            complete: function(jqXHR, textStatus) {

                                $('#pay-invoice-submit').html(pay_button);
                                $('#pay-invoice-submit').data('changed', 'changed');
                            }
                        });
                    }
                }
            });
        } else {

            // check if the button has been changed at any point

            if ($('#pay-invoice-submit').data('changed') == 'changed') {

                // it has, load the default button
                var default_url = $(this).data('default-button-url');
                $.ajax({
                    'url': default_url,
                    'cache': false,
                    success: function(data, textStatus, jqXHR) {

                        data = $.parseJson(data);
                        if (data.result && data.html != '') {

                            $('#pay-invoice-submit').html(data.html);
                            $('#pay-invoice-submit').removeData('changed');
                        }
                    }
                });
            }
        }
    });
});