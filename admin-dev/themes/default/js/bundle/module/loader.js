$(function() {
    $("#module-import").click(function() {
        $("#module-import").addClass("onclick", 250, validate);
    });

    function validate() {
        setTimeout(function() {
            $("#module-import").removeClass("onclick");
            $("#module-import").addClass("validate", 450, callback);
        }, 2250 );
    }
    function callback() {
        setTimeout(function() {
            $("#module-import").removeClass("validate");
        }, 1250 );
    }

    $('body').on('show.bs.modal', '.modal', function (event) {
        var urlCallModule = event.relatedTarget.href;
        var modulePoppin = $(event.relatedTarget).data('target');

        $.get(urlCallModule, function (data) {
            $(modulePoppin).html(data);
        });
    });
});
