import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(() => {
  prestashop.on('cart dom updated', function(event) {
    createSpin();
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
  $('body').on(
    'click',
    '[data-button-action="add-to-cart"]',
    function(event) {
      event.preventDefault();
      var $form = $($(event.target).closest('form'));
      var query = $form.serialize() + '&add=1&action=update';
      var actionURL = $form.attr('action');

      $.post(actionURL, query, null, 'json').then(function(resp) {
        $('.quickview').modal('hide');
        prestashop.emit('cart updated', {
          reason: {
            idProduct: resp.id_product,
            idProductAttribute: resp.id_product_attribute,
            linkAction: 'add-to-cart'
          }
        });
      });
    }
  );

  $('body').on(
    'submit',
    '[data-link-action="add-voucher"]',
    function(event) {
      event.preventDefault();

      $(this).append($('<input>')
        .attr('type', 'hidden')
        .attr('name', 'ajax').val('1')
      );
      $(this).append($('<input>')
        .attr('type', 'hidden')
        .attr('name', 'action').val('update')
      );

      // First perform the action using AJAX
      var actionURL = $(this).attr('action');

      $.post(actionURL, $(this).serialize(), null, 'json').then(function() {
        // If succesful, refresh cart preview
        prestashop.emit('cart updated', {
          reason: event.target.dataset
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
