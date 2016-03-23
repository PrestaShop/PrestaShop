import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(() => {
  prestashop.on('quickview clicked', function (elm) {
    let data = {
      'action' : 'quickview',
      'id_product' : elm.dataset.productId,
      'id_product_attribute' : elm.dataset.productIdAttribute,
    };
    $.post(prestashop.urls.pages.product, data, null, 'json').then(function(resp) {
      $('body').append(resp.quickview_html);
      $('#quickview-modal-' + resp.product.id + '-' + resp.product.id_product_attribute).modal('show');
    });
  });
});
