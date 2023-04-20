import $ from 'jquery';
import prestashop from 'prestashop';

/**
 * Update address form on country change
 * Emit "addressFormUpdated" event
 *
 * @param selectors
 */
function handleCountryChange(selectors) {
  $('body').on('change', selectors.country, () => {
    const requestData = {
      id_country: $(selectors.country).val(),
      id_address: $(`${selectors.address} form`).data('id-address'),
    };
    const getFormViewUrl = $(`${selectors.address} form`).data('refresh-url');
    const formFieldsSelector = `${selectors.address} input`;

    $.post(getFormViewUrl, requestData).then((resp) => {
      const inputs = [];

      // Store fields values before updating form
      $(formFieldsSelector).each(function () {
        inputs[$(this).prop('name')] = $(this).val();
      });

      $(selectors.address).replaceWith(resp.address_form);

      // Restore fields values
      $(formFieldsSelector).each(function () {
        $(this).val(inputs[$(this).prop('name')]);
      });

      prestashop.emit('updatedAddressForm', {target: $(selectors.address), resp});
    }).fail((resp) => {
      prestashop.emit('handleError', {eventType: 'updateAddressForm', resp});
    });
  });
}

$(() => {
  handleCountryChange({
    country: '.js-country',
    address: '.js-address-form',
  });
});
