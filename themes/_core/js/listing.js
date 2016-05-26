import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(function () {
  $('body').on('click', '.quick-view', function (event) {
    prestashop.emit('quickview clicked', {
      dataset: event.target.closest('.js-product-miniature').dataset
    });
    event.preventDefault();
  })
});
