$(document).ready(function() {

    $('a[title="Add a module"]').attr('data-toggle', 'modal');
    $('a[title="Add a module"]').attr('data-target', '#module-modal-read-more');

    var importC = new AdminModuleImport();
    importC.init();

});

/**
 * AdminModuleImport Page Controller.
 * @constructor
 */
var AdminModuleImport = function() {

    /**
     * Initialize all listners and bind everything
     * @method init
     * @memberof AdminModule
     */
    this.init = function() {
        this.initDropzone();
    };

    this.initDropzone = function () {
        Dropzone.options.importDropzone = {
            url: 'import',
            acceptedFiles: '.zip, .tar',
            paramName: "module_file", // The name that will be used to transfer the file
            maxFilesize: 5, // MB
            uploadMultiple: false,
            addRemoveLinks: true,
            processing: function (file, response) {
                $('.dz-preview').css('display', 'none');
                $('.module-import-loader').css('display', 'block');
                $('.install-message').css('display', 'block');
                $( ".module-import-loader" ).addClass( "onclic" );
            },
            complete: function (file, response) {
                setTimeout(function() {
                    $( ".module-import-loader" ).removeClass( "onclic" );
                    $( ".module-import-loader" ).addClass( "validate" );
                }, 2250 );
            }
        };
    };

};
