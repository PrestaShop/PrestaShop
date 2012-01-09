$(document).ready(function (){
    displayHelpButton(
        help_class_name,
        iso_user,
        country_iso_code,
        _PS_VERSION_
    );
});

function displayHelpButton(label, iso_user, country_iso_code, _PS_VERSION_){
    var button = '';
    $.ajax({
        type: 'POST',
        url: 'index.php',
        data: {
            'ajax' : 1,
            'action' : 'helpAccess',
            'item' : label,
            'isoUser' : iso_user,
            'country' : country_iso_code,
            'version' : _PS_VERSION_
        },
        dataType: "json",
        success: function(msg) {
            if(msg.content != 'none' && msg.content != '')
            {
                $('ul.cc_button').append(msg.content);
                $('.help-context-'+help_class_name).fadeIn("fast").show();
            }
        }
    });
}