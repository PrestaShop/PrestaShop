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

    // TODO : Get the URL from the form
    $.post('http://prestashop-develop.com/en/cart?ajax=1&action=addressForm', requestData).then(function (resp) {
      var inputs = [];
      $('.js-address-form input').each(function () {
        inputs[$(this).prop('name')] = $(this).val();
      });

      $('.js-address-form').replaceWith(resp.address_form);

      // TODO : Fill the inputs with the saved values

      prestashop.emit('address form updated');
    });
  });
}
