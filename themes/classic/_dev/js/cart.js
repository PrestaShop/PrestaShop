import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(() => {
  prestashop.on('cart dom updated', function(event) {
    createSpin();
  });
  prestashop.on('cart updated', function(event) {
    $('.quickview').modal('hide');
  });

  $('body').on(
    'click',
    '.js-touchspin, [data-link-action="delete-from-cart"], [data-link-action="remove-voucher"]',
    function(event) {
      event.preventDefault();
      // First perform the action using AJAX
      var actionURL = null;

      if ($(event.currentTarget).hasClass('bootstrap-touchspin-up')) {
        actionURL = $(event.currentTarget).parents('.bootstrap-touchspin').find('[data-up-url]').data('up-url');
      } else if ($(event.currentTarget).hasClass('bootstrap-touchspin-down')) {
        actionURL = $(event.currentTarget).parents('.bootstrap-touchspin').find('[data-up-url]').data('down-url');
      } else{
        actionURL = $(event.currentTarget).attr('href');
      }

      $.post(actionURL, {
        ajax: '1',
        action: 'update'
      }, null, 'json').then(function() {
        // If succesful, refresh cart preview
        prestashop.emit('cart updated', {
          reason: event.currentTarget.dataset
        });
      });
    }
  );

  createSpin();
});

function createSpin()
{
  $('input[name="product-quantity-spin"]').TouchSpin({
    verticalbuttons: true,
    verticalupclass: 'material-icons touchspin-up',
    verticaldownclass: 'material-icons touchspin-down',
    buttondown_class: 'btn btn-touchspin js-touchspin',
    buttonup_class: 'btn btn-touchspin js-touchspin'
  });
}
