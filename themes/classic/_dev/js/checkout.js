import $ from 'jquery';
import prestashop from 'prestashop';

function setUpCheckout() {
  if ($('.js-cancel-address').length !== 0) {
    $('.checkout-step:not(.js-current-step) .step-title').addClass('not-allowed');
  }

  $('.js-terms a').on('click', (event) => {
    event.preventDefault();
    var url = $(event.target).attr('href');
    if (url) {
      // TODO: Handle request if no pretty URL
      url += `?content_only=1`;
      $.get(url, (content) => {
        $('#modal').find('.modal-content').html($(content).find('.page-cms').contents());
      }).fail((resp) => {
        prestashop.emit('handleError', {eventType: 'clickTerms', resp: resp});
      });
    }

    $('#modal').modal('show');
  });

  $('.js-gift-checkbox').on('click', (event) => {
    $('#gift').collapse('toggle');
  });
}

$(document).ready(() => {
  if ($('body#checkout').length === 1) {
    setUpCheckout();
  }

  prestashop.on('updatedDeliveryForm', (params) => {
    // Hide all carrier extra content ...
    $(".carrier-extra-content").hide();
    // and show the one related to the selected carrier
    params.deliveryOption.find(".carrier-extra-content").slideDown();
  });
});
