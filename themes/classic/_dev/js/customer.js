import $ from 'jquery';

function initRmaItemSelector() {
  $('#order-return-form table thead input[type=checkbox]').on('click', function() {
    var checked = $(this).prop('checked');
    $('#order-return-form table tbody input[type=checkbox]').each(function(_, checkbox) {
      $(checkbox).prop('checked', checked);
    });
  });
}

function setupCustomerScripts() {
  if ($('body#order-detail')) {
    initRmaItemSelector();
  }
}

$(document).ready(setupCustomerScripts);
