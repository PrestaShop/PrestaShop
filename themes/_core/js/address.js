/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import prestashop from 'prestashop';

/**
 * Update address form on country change
 * Emit "addressFormUpdated" event
 *
 * @param selectors
 */
function handleCountryChange(selectors) {
  $('body').on('change', selectors.country, (event) => {
    const target = $(event.target);

    // The checkout shows the delivery and the invoice address form at the same time,
    // so every lookup below has to be scoped to the form that actually changed. A
    // page-wide selector reads the first form on the page instead, which is why
    // changing the country on the invoice form used the delivery form's country.
    const addressForm = target.closest(selectors.address);

    // In the checkout the address partial renders inside the step's own <form> and
    // browsers drop the nested start tag, so the data attributes end up on the
    // enclosing form. On the my-account address page they stay inside the wrapper.
    const dataHolder = addressForm.find('form').first().length
      ? addressForm.find('form').first()
      : target.closest('form');

    const requestData = {
      id_country: target.val(),
      id_address: dataHolder.data('id-address'),
    };
    const getFormViewUrl = dataHolder.data('refresh-url');

    const submitButton = addressForm.find('[type="submit"]');
    submitButton.prop('disabled', true);

    $.post(getFormViewUrl, requestData).then((resp) => {
      const inputs = [];

      // Store fields values before updating form
      addressForm.find('input').each(function () {
        inputs[$(this).prop('name')] = $(this).val();
      });

      const updatedForm = $(resp.address_form);
      addressForm.replaceWith(updatedForm);

      // Restore fields values
      updatedForm.find('input').each(function () {
        $(this).val(inputs[$(this).prop('name')]);
      });

      prestashop.emit('updatedAddressForm', {target: updatedForm, resp});
    }).fail((resp) => {
      submitButton.prop('disabled', false);
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
