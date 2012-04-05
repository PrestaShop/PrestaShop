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
        },
		beforeSend: function(data)
 		{
 			// don't display the loading notification bar
 			clearTimeout(ajax_running_timeout);
 		}
    });
}

function showHelp(url, label, iso_lang, ps_version, doc_version, country)
{
    trackClickOnHelp(label, doc_version);
    $('.help-context-'+label+' span').removeClass().addClass('process-icon-help');
    window.open(url +'/'+iso_lang+'/doc/'+label+'?version='+ps_version+'&country='+country+'#', '_blank', 'scrollbars=yes,menubar=no,toolbar=no,location=no,width=517,height=600');
    return false;
}


function trackClickOnHelp(label, doc_version)
{
    $.ajax({
        url: 'ajax.php',
        data: 'submitTrackClickOnHelp&label='+ label +'&version='+doc_version
    });
}