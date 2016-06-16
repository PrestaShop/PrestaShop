import $ from 'jquery';
import prestashop from 'prestashop';

$(document).ready(() => {
  prestashop.on('address form updated', function(event) {
    changeCountry();
  });

  changeCountry();
});

function changeCountry() {
  $('.js-country').change(function () {
    var requestData = {
      id_country: $('.js-country').val(),
      id_address: $('.js-address-form form').data('id-address'),
    };

    $.post($('.js-address-form form').data('link-update'), requestData).then(function (resp) {
      var inputs = [];
      $('.js-address-form input').each(function () {
        inputs[$(this).prop('name')] = $(this).val();
      });

      $('.js-address-form').replaceWith(resp.address_form);

      $('.js-address-form input').each(function () {
        $(this).val(inputs[$(this).prop('name')]);
      });

      prestashop.emit('address form updated');
    });
  });
}
