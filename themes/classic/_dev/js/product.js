/* global document */

import $ from 'jquery';

$(document).ready(function () {
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
});
