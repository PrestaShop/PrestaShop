import $ from 'jquery';

function setupMyCheckoutScripts() {
  if ($('.js-cancel-address').length !== 0) {
    $('.checkout-step:not(.-js-current) .step-title').addClass('not-allowed');
  }

  $('body').on('click', '.checkout-step.-reachable h1', function(event) {
    if ($('.js-cancel-address').length === 0) {
      $('.-js-current, .-current').removeClass('-js-current -current');
      $(event.target).closest('.checkout-step').toggleClass('-js-current');
    }
  });

  $('.js-terms a').on('click', (event) => {
    event.preventDefault();
    $('#modal').modal('show');
  });

  var url = $('.js-terms a').attr('href');
  if (url) {
    // TODO: Handle request if no pretty URL
    url += `?content_only=1`;
    $.get(url, (content) => {
      $('#modal').find('.modal-content').html($(content).find('.page-cms').contents());
    });
  }
}

$(document).ready(() => {
  if ($('body#checkout').length === 1) {
    setupMyCheckoutScripts();
  }
});
