$(document).ready(function() {

    $('a[title="Add a module"]').attr('data-toggle', 'modal');
    $('a[title="Add a module"]').attr('data-target', '#module-modal-read-more');

    Dropzone.options.importDropzone = {
        url: 'import',
        acceptedFiles: '.zip, .tar',
        paramName: "module_file", // The name that will be used to transfer the file
        maxFilesize: 5, // MB
        uploadMultiple: false,
        addRemoveLinks: true,
        accept: function(file, done) {
            console.log(file);
        },
        success: function (file, response) {
            console.log(response);
        }
    };
});
