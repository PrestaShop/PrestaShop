$(function() {
    $( "#module-import" ).click(function() {
        $( "#module-import" ).addClass( "onclick", 250, validate);
    });

    function validate() {
        setTimeout(function() {
            $( "#module-import" ).removeClass( "onclick" );
            $( "#module-import" ).addClass( "validate", 450, callback );
        }, 2250 );
    }
    function callback() {
        setTimeout(function() {
            $( "#module-import" ).removeClass( "validate" );
        }, 1250 );
    }
});
