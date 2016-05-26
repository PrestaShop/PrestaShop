import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(() => {
  prestashop.on('cart updated', function (event) {
    var refreshURL = $('.-js-cart').data('refresh-url');
    var requestData = {};

    if (event && event.reason) {
      requestData = {
        id_product_attribute: event.reason.idProductAttribute,
        id_product: event.reason.idProduct
      };
    }

    $.post(refreshURL, requestData).then(function (resp) {
      $('.cart-overview').replaceWith(resp.cart_detailed);
      $('.cart-detailed-totals').replaceWith(resp.cart_detailed_totals);
      $('.cart-summary-items-subtotal').replaceWith(resp.cart_summary_items_subtotal);
      $('.cart-summary-totals').replaceWith(resp.cart_summary_totals);
      $('.cart-detailed-actions').replaceWith(resp.cart_detailed_actions);
      $('.cart-voucher').replaceWith(resp.cart_voucher);

      prestashop.emit('cart dom updated');
    });
  });
});
