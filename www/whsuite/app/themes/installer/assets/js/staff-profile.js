$(document).ready(function() {

    $( ".connectedSortable-shortcuts" ).sortable({
        connectWith: ".connectedSortable-shortcuts"
    }).disableSelection();

    $( ".connectedSortable-widgets" ).sortable({
        connectWith: ".connectedSortable-widgets"
    }).disableSelection();


    /**
     * on submit of the profile form generate a json encoded string
     * of dashboard order
     */
    $('#profile-save').bind('submit', function(e) {

        generateIdList('shortcut');

        generateIdList('widget');
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
    var dashboard_ids = new Array();

    $('#selected.connectedSortable-' + type + 's').find('li').each(function(i, e) {

        dashboard_ids.push($(this).data(type + '-id'));
    });

    dashboard_ids = dashboard_ids.join(',');

    // uppercase first letter so we get Widget and Shortcut
    type = type.charAt(0).toUpperCase() + type.slice(1);
    $('#dataStaff' + type).val(dashboard_ids);
}

