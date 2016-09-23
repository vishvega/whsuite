$(document).ready(function() {

    $( ".connectedSortable-gateways" ).sortable({
        connectWith: ".connectedSortable-gateways"
    }).disableSelection();


    /**
     * on submit of the profile form generate a json encoded string
     * of dashboard order
     */
    $('#currency-save').bind('submit', function(e) {

        generateIdList('gateway');
    });

});

/**
 * convert the selected list of widgets / shortcuts
 * from an ul list into a comma seperated string of ids
 * to pass in form to be saved
 *
 * @param   string  identifying string for class names / form id
 */
function generateIdList(type)
{
    var currency_ids = new Array();

    $('#selected.connectedSortable-' + type + 's').find('li').each(function(i, e) {

        currency_ids.push($(this).data(type + '-id'));
    });

    currency_ids = currency_ids.join(',');

    // uppercase first letter so we get Widget and Shortcut
    type = type.charAt(0).toUpperCase() + type.slice(1);
    $('#dataCurrency' + type).val(currency_ids);
}

