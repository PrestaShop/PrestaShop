function getHelpButton(label){
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
        async : false,
        success: function(msg) {
            if(msg.content != 'none' && msg.content != '')
                button = msg.content;
        }
    });

    return button;
}