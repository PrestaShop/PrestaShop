import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(function () {
  $('body').on('change', '.product-variants [data-product-attribute], #quantity_wanted', function () {
    $("input[name$='refresh']").click();
  });

  prestashop.on('product updated', function(event) {
    $.post(event.reason.productUrl, {ajax: '1', action: 'refresh'}, null, 'json').then(function(resp) {
      $('.product-prices').replaceWith(resp.product_prices);
      $('.product-variants').replaceWith(resp.product_variants);
      $('.images-container').replaceWith(resp.product_cover_thumbnails);
      $('#product-details').replaceWith(resp.product_details);

      window.history.pushState({id_product_attribute: resp.id_product_attribute}, undefined, resp.product_url);
    });
  });
});
