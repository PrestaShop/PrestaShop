/* global document */

import $ from 'jquery';

$(document).ready(function () {
  $('body').on('change', '.product-variants [data-product-attribute], #quantity_wanted', function () {
    $("input[name$='refresh']").click();
  });
});
