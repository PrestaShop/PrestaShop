var modules_list = new Array();
var hooks_list = new Array();
var hookable_list = new Array();
var timer;
$(document).ready(function() {

    $('body').css('margin-bottom', '45px');
    $('#fancy').fancybox({
        autoDimensions: true,
        autoScale: true,
        width: 300,
        height: 300,
        padding: 0,
        hideOnOverlayClick: false,
        hideOnContentClick: false,
        showCloseButton: false
    });
    $('#live_edit_feedback_str').html('');
    $('a').each(function() {
        var href = $(this).attr('href');
        var search = $(this).attr('search');
        var hrefAdd = 'live_edit&liveToken=' + get('liveToken') + '&ad=' + get('ad');
        if (href != undefined && href != '#' && href.substr(0, baseDir.length) == baseDir) {
            if (search.length == 0) {
                $(this).attr('search', hrefAdd);
            }
            else {
                $(this).attr('search', search + '&' + hrefAdd);
            }
        }
    });
    getHookableList();
    $('.unregisterHook').unbind('click').click(function() {
        id = $(this).attr('id');
        $(this).parent().parent().parent().fadeOut('slow', function() {
            $(this).remove();
        });
        return false;
    });

    $('#cancelMove').unbind('click').click(function() {
        $('#' + cancelMove + '').sortable('cancel');
        return false;
    });

    $('#saveLiveEdit').unbind('click').click(function() {
        saveModulePosition();
        return false;
    });

    $('#closeLiveEdit').unbind('click').click(function() {
        $("#live_edit_feedback_str").html('<div style="padding:10px;"><p style="margin-bottom:10px;">' + confirmClose + '</p><p style="height:1.6em;display:block"><a style="margin:auto;float:left" class="button" href="#" onclick="closeLiveEdit();">' + confirm + '</a><a style="margin:auto;float:right;" class="button" href="#" onclick="closeFancybox();">' + cancel + '</a></p></div>');
        $("#fancy").attr('href', '#live_edit_feedback');
        $("#fancy").trigger("click");
    });

    $('.add_module_live_edit').unbind('click').click(function() {
        $("#live_edit_feedback_str").html('<div style="padding:10px"><img src="img/loadingAnimation.gif"></div>');
        $("#fancy").attr('href', '#live_edit_feedback');
        $("#fancy").trigger("click");
        var id = $(this).attr('id');
        getHookableModuleList(id.substr(4, id.length));
        return false;
    });

    $('.dndHook').each(function() {
        var id_hook = $(this).attr('id');
        var new_target = '';
        var old_target = '';
        var cancel = false;

        $('#' + id_hook + '').sortable({
            opacity: 0.5,
            cursor: 'move',

            connectWith: '.dndHook',
            receive: function(event, ui) {
                if (new_target == '') {
                    new_target = event.target.id;
                }
            },
            start: function(event, ui) {
                new_target = ui.item[0].parentNode.id;
            },
            stop: function(event, ui) {

                if (cancel) {
                    $(this).sortable('cancel');
                }
                else {
                    old_target = event.target.id;
                    cancelMove = old_target;
                    if (new_target == '') new_target = old_target;
                }
            },
            change: function(event, ui) {
                new_target = $(ui.placeholder).parent().attr('id');
                ids = ui.item[0].id.split('_');
                if ($.inArray(ids[5], hookable_list[new_target]) != -1) {
                    cancel = false;
                    ui.placeholder.css({
                        visibility: 'visible',
                        border: '1px solid #72CB67',
                        background: '#DFFAD3'
                    });
                }
                else {
                    ui.placeholder.css({
                        visibility: 'visible',
                        border: '1px solid #EC9B9B',
                        background: '#FAE2E3'
                    });
                    cancel = true;
                }
            }
        });
        $('#' + id_hook + '').disableSelection();
    });
});

function getHookableList() {
    $.ajax({
        type: 'GET',
        url: baseDir + ad + '/ajax.php',
        async: true,
        dataType: 'json',
        data: 'ajax=true&getHookableList&hooks_list=' + hooks_list + '&modules_list=' + modules_list,
        success: function(jsonData) {
            hookable_list = jsonData;
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#live_edit_feedback_str').html('<div class="live_edit_feed_back_ko"><img src="img/admin/error.png"><h3>TECHNICAL ERROR:</h3>' + loadFail + '<br><br><a style="margin:auto" class="button" href="#" onclick="closeFancybox();">' + close + '</a></div>');
            $("#fancy").attr('href', '#live_edit_feedback');
            $("#fancy").trigger("click");
        }
    });
}

function getHookableModuleList(hook) {

    $.ajax({
        type: 'GET',
        url: baseDir + ad + '/ajax.php',
        async: true,
        dataType: 'json',
        data: 'ajax=true&getHookableModuleList&hook=' + hook,
        success: function(jsonData) {

            var select = '<select id="select_module">';
            for (var i = 0; i < jsonData.length; i++) {
                select += '<option value="' + jsonData[i].id + '">' + jsonData[i].name + '</option>';
            }
            select += '</select>';
            $("#live_edit_feedback_str").html('<div style="padding:10px">' + select + '<br><br><a style="margin:auto" class="button" href="#" >' + add + '</a><br><br><a style="margin:auto" class="button" href="#" onclick="closeFancybox();">' + cancel + '</a>');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("TECHNICAL ERROR: unable to unregister hook \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
        }
    });
}

function saveModulePosition() {
    $("#live_edit_feedback_str").html('<div style="padding:10px"><img src="img/loadingAnimation.gif"></div>');
    $("#fancy").attr('href', '#live_edit_feedback');
    $("#fancy").trigger("click");
    var str = '';
    for (var i = 0; i < hooks_list.length; i++) {
        str += '&' + hooks_list[i] + '=';
        $('#' + hooks_list[i] + ' > .dndModule').each(function() {
            ids = $(this).attr('id').split('_');
            str += ids[1] + '_' + ids[3] + ',';
        });
        str = str.substr(0, str.length - 1);
    }
    $.ajax({
        type: 'GET',
        url: baseDir + ad + '/ajax.php',
        async: true,
        dataType: 'json',
        data: 'ajax=true&saveHook&hooks_list=' + hooks_list + str,
        success: function(jsonData) {
            $('#live_edit_feedback_str').html('<div class="live_edit_feed_back_ok"><img src="img/admin/ok2.png"><h3>' + saveOK + '</h3><a style="margin:auto" class="exclusive" href="#" onclick="closeFancybox();">' + close + '</a></div>');
            timer = setTimeout("hideFeedback()", 3000);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#live_edit_feedback_str').html('<div class="live_edit_feed_back_ko"><img src="img/admin/error.png"><h3>TECHNICAL ERROR:</h3>' + unableToSaveModulePosition + '<br><br><a style="margin:auto" class="button" href="#" onclick="closeFancybox();">' + close + '</a></div>');
        }
    });
}

function closeFancybox() {
    clearTimeout(timer);
    $.fancybox.close();
    $('#live_edit_feedback_str').html('');
}

function closeLiveEdit(){
	window.location.href = window.location.protocol+'//'+window.location.host+window.location.pathname;
}

function hideFeedback() {
    $('#live_edit_feed_back').fadeOut('slow', function() {
        $.fancybox.close();
        $('#live_edit_feedback_str').html('');
    });
};

function get(name) {
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.href);
    if (results == null) {
        return "";
    }
    else {
        return results[1];
    }
}
