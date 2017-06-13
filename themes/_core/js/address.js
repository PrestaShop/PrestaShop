import $ from 'jquery';
import prestashop from 'prestashop';

/**
 * Update address form on country change
 * Emit "addressFormUpdated" event
 *
 * @param selectors
 */
function handleCountryChange (selectors) {
  $('body').on('change', selectors.country, () => {
    var requestData = {
      id_country: $(selectors.country).val(),
      id_address: $(selectors.address + ' form').data('id-address'),
    };
    var getFormViewUrl = $(selectors.address + ' form').data('refresh-url');
    var formFieldsSelector = selectors.address + ' input';

    $.post(getFormViewUrl, requestData).then(function (resp) {
      var inputs = [];

      // Store fields values before updating form
      $(formFieldsSelector).each(function () {
        inputs[$(this).prop('name')] = $(this).val();
      });

      $(selectors.address).replaceWith(resp.address_form);

      // Restore fields values
      $(formFieldsSelector).each(function () {
        $(this).val(inputs[$(this).prop('name')]);
      });

      prestashop.emit('updatedAddressForm', {target: $(selectors.address), resp: resp});
    }).fail((resp) => {
      prestashop.emit('handleError', {eventType: 'updateAddressForm', resp: resp});
    });
  });
}

$(document).ready(() => {
  handleCountryChange({
    'country': '.js-country',
    'address': '.js-address-form'
  });
});
