import $ from 'jquery';

$(document).ready(function () {
  createProductSpin();
  createInputFile();
  coverImage();
  imageScrollBox();

  $('body').on(
    'click',
    '.product-refresh',
    function (event, extraParameters) {
      var $productRefresh = $(this);
      event.preventDefault();

      let eventType = 'updatedProductCombination';
      if (typeof extraParameters !== 'undefined' && extraParameters.eventType) {
        eventType = extraParameters.eventType;
      }

      var query = $(event.target.form).serialize() + '&ajax=1&action=productrefresh';
      var actionURL = $(event.target.form).attr('action');

      $.post(actionURL, query, null, 'json').then(function(resp) {
        prestashop.emit('updateProduct', {
          reason: {
           productUrl: resp.productUrl
          },
          refreshUrl: $productRefresh.data('url-update'),
          eventType: eventType
        });
      });
    }
  );

  prestashop.on('updatedProduct', function (event) {
    createInputFile();
    coverImage();
    if (event && event.product_minimal_quantity) {
      const minimalProductQuantity = parseInt(event.product_minimal_quantity, 10);
      const quantityInputSelector = '#quantity_wanted';
      let quantityInput = $(quantityInputSelector);

      // @see http://www.virtuosoft.eu/code/bootstrap-touchspin/ about Bootstrap TouchSpin
      quantityInput.trigger('touchspin.updatesettings', {min: minimalProductQuantity});
    }
    imageScrollBox();
    $($('.tabs .nav-link.active').attr('href')).addClass('active').removeClass('fade');
    $('.js-product-images-modal').replaceWith(event.product_images_modal);
  });

  function coverImage() {
    $('.js-thumb').on(
      'click',
      (event) => {
        $('.js-modal-product-cover').attr('src',$(event.target).data('image-large-src'));
        $('.selected').removeClass('selected');
        $(event.target).addClass('selected');
        $('.js-qv-product-cover').prop('src', $(event.currentTarget).data('image-large-src'));
      }
    );
  }

  function imageScrollBox()
  {
    if ($('#main .js-qv-product-images li').length > 2) {
      $('#main .js-qv-mask').addClass('scroll');
      $('.scroll-box-arrows').addClass('scroll');
        $('#main .js-qv-mask').scrollbox({
          direction: 'h',
          distance: 113,
          autoPlay: false
        });
        $('.scroll-box-arrows .left').click(function () {
          $('#main .js-qv-mask').trigger('backward');
        });
        $('.scroll-box-arrows .right').click(function () {
          $('#main .js-qv-mask').trigger('forward');
        });
    } else {
      $('#main .js-qv-mask').removeClass('scroll');
      $('.scroll-box-arrows').removeClass('scroll');
    }
  }

  function createInputFile()
  {
    $('.js-file-input').on('change',(event)=>{
      $('.js-file-name').text($(event.currentTarget).val());
    });
  }

  function createProductSpin()
  {
    let quantityInput = $('#quantity_wanted');
    quantityInput.TouchSpin({
      verticalbuttons: true,
      verticalupclass: 'material-icons touchspin-up',
      verticaldownclass: 'material-icons touchspin-down',
      buttondown_class: 'btn btn-touchspin js-touchspin',
      buttonup_class: 'btn btn-touchspin js-touchspin',
      min: parseInt(quantityInput.attr('min'), 10),
      max: 1000000
    });

    quantityInput.on('change', function (event) {
      let $productRefresh = $('.product-refresh');
      $(event.currentTarget).trigger('touchspin.stopspin');
      $productRefresh.trigger('click', {eventType: 'updatedProductQuantity'});
      event.preventDefault();

      return false;
    });
  }
});
