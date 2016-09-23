
$(document).ready(function() {

    if ($('.dashboard-widget').length > 0) {

        $('.dashboard-widget').each(function(i, e) {

            var id = $(this).attr('id');
            var url = $(this).data('widget-url');
            var content = '';

            $.ajax({
                'url': url,
                'cache': false,
                success: function(data, textStatus, jqXHR) {

                    content = data;
                },
                complete: function(jqXHR, textStatus) {

                    $('#' + id).replaceWith(content);
                }
            });

        });
    }

    if ($('.shortcut-label').length > 0) {

        $('.shortcut-label').each(function(i, e) {

            var div_loader = $(this);
            var id = $(this).parent().attr('id');
            var url = $(this).data('label-route');
            var content = '';

            $.ajax({
                'url': url,
                'cache': false,
                success: function(data, textStatus, jqXHR) {

                    content = '<span id="' + id + '_label" class="label label-danger" style="display: none;">' + data + '</span>';
                },
                complete: function(jqXHR, textStatus) {

                    div_loader.replaceWith(content);
                    if (content != '') {

                        $('#' + id + '_label').fadeIn();
                    }
                }
            });

        });
    }





});
