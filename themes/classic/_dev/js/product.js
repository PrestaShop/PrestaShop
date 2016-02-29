/* global document */

import $ from 'jquery';

$(document).ready(function () {
  prestashop.on('product updated', function(event) {
    $.post(event.reason.productUrl, {productajax: '1'}, null, 'json').then(function(resp) {
      $('.product-prices').replaceWith(resp.product_prices);
      $('.product-variants').replaceWith(resp.product_variants);
      $('.images-container').replaceWith(resp.product_cover_thumbnails);
      $('#product-details').replaceWith(resp.product_details);

      window.history.pushState({id_product_attribute: resp.id_product_attribute}, undefined, resp.product_url);
    });
  });

  $('body').on('change', '.product-variants [data-product-attribute], #quantity_wanted', function () {
    $("input[name$='refresh']").click();
  });

  $('.js-file-input').on('change',(event)=>{
    $('.js-file-name').text($(event.currentTarget).val());
  });

  $('#quantity_wanted').TouchSpin({
    verticalbuttons: true,
    verticalupclass: 'material-icons touchspin-up',
    verticaldownclass: 'material-icons touchspin-down',
    buttondown_class: 'btn btn-touchspin js-touchspin',
    buttonup_class: 'btn btn-touchspin js-touchspin'
  });

  $('body').on(
    'click',
    'input.product-refresh',
    function(event) {
      event.preventDefault();

      var query = $(event.target.form).serialize() + '&productajax=1';
      var actionURL = $(event.target.form).attr('action');

      $.post(actionURL, query, null, 'json').then(function(resp) {
        prestashop.emit('product updated', {
          reason: {
            productUrl: resp.productUrl,
          }
        });
      });
    }
  );

});
