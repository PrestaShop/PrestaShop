$(function() {
  var moduleImport = $("#module-import");
  moduleImport.click(function() {
    moduleImport.addClass("onclick", 250, validate);
  });

  function validate() {
    setTimeout(function() {
      moduleImport.removeClass("onclick");
      moduleImport.addClass("validate", 450, callback);
    }, 2250 );
  }
  function callback() {
    setTimeout(function() {
      moduleImport.removeClass("validate");
    }, 1250 );
  }

  $('body').on('show.bs.modal', '.ps-modal-card', function (event) {
    var urlCallModule = event.relatedTarget.href;
    var modulePoppin = $(event.relatedTarget).data('target');

    $.get(urlCallModule, function (data) {
      $(modulePoppin).html(data);
    });
  });
});
