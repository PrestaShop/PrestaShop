import $ from 'jquery';

function initRmaItemSelector() {
  $('#order-return-form table thead input[type=checkbox]').on('click', function() {
    var checked = $(this).prop('checked');
    $('#order-return-form table tbody input[type=checkbox]').each(function(_, checkbox) {
      $(checkbox).prop('checked', checked);
    });
  });

  $('form[data-toggle="validator"]').validator().on('invalid.bs.validator', (e) => {
    setTimeout(() => {
      $(e.relatedTarget).next('.with-errors').removeClass('hidden-xs-up');
    }, 500);

  });
  $('form[data-toggle="validator"]').validator().on('valid.bs.validator', (e) => {
    $(e.relatedTarget).next('.with-errors').addClass('hidden-xs-up');
  });
}

function setupCustomerScripts() {
  if ($('body#order-detail')) {
    initRmaItemSelector();
  }
}

$(document).ready(setupCustomerScripts);
