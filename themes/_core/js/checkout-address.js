/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import prestashop from 'prestashop';
import {psGetRequestParameter} from './common';

const {addressForm} = prestashop.selectors.checkout;
const editAddress = psGetRequestParameter('editAddress');
const useSameAddress = psGetRequestParameter('use_same_address');

function updateAddressForm() {
  const $addressForm = $(`${addressForm} form`);
  const requestData = $addressForm.serialize();
  const confirmedRequestData = new URLSearchParams(requestData);
  confirmedRequestData.append('confirm-addresses', 1);

  $.post($addressForm.data('refresh-url'), confirmedRequestData.toString())
    .fail((resp) => {
      prestashop.emit('handleError', {
        eventType: 'updatedAddressForm',
        resp,
      });
    });
}

export default function () {
  $(prestashop.selectors.checkout.editAddresses).on('click', (event) => {
    event.stopPropagation();
    $(prestashop.selectors.checkout.addressesStep).trigger('click');
    prestashop.emit('editAddress');
  });

  $(prestashop.selectors.checkout.deliveryAddressRadios).on(
    'click',
    function () {
      $(prestashop.selectors.checkout.addressItem).removeClass('selected');
      $(prestashop.selectors.checkout.addressItemChecked).addClass('selected');
      updateAddressForm();

      const idFailureAddress = $(prestashop.selectors.checkout.addressError)
        .prop('id')
        .split('-')
        .pop();
      const notValidAddresses = $(
        prestashop.selectors.checkout.notValidAddresses,
      ).val();
      const addressType = this.name.split('_').pop();
      const $addressError = $(
        `${prestashop.selectors.checkout.addressError}[name=alert-${addressType}]`,
      );

      switchEditAddressButtonColor(false, idFailureAddress, addressType);

      if (notValidAddresses !== '' && editAddress === null) {
        if (notValidAddresses.split(',').indexOf(this.value) >= 0) {
          $addressError.show();
          switchEditAddressButtonColor(true, this.value, addressType);
          $(prestashop.selectors.checkout.addressError).prop(
            'id',
            `id-failure-address-${this.value}`,
          );
        } else {
          $addressError.hide();
        }
      } else {
        $addressError.hide();
      }

      const $visibleAddressError = $(
        `${prestashop.selectors.checkout.addressError}:visible`,
      );
      switchConfirmAddressesButtonState($visibleAddressError.length <= 0);
    },
  );
}

$(window).on('load', () => {
  let $visibleAddressError = $(
    `${prestashop.selectors.checkout.addressError}:visible`,
  );

  if (parseInt(useSameAddress, 10) === 0) {
    $(prestashop.selectors.checkout.invoiceAddresses).trigger('click');
  }
  if (
    editAddress !== null
    || $(`${prestashop.selectors.checkout.addressForm}:visible`).length > 1
  ) {
    $visibleAddressError.hide();
  }

  if ($visibleAddressError.length > 0) {
    const idFailureAddress = $(prestashop.selectors.checkout.addressError)
      .prop('id')
      .split('-')
      .pop();

    $visibleAddressError.each(function () {
      switchEditAddressButtonColor(
        true,
        idFailureAddress,
        $(this)
          .attr('name')
          .split('-')
          .pop(),
      );
    });
  }
  $visibleAddressError = $(
    `${prestashop.selectors.checkout.addressError}:visible`,
  ); // Refresh after possible hide
  switchConfirmAddressesButtonState($visibleAddressError.length <= 0);
});

/**
 * Change the color of the edit button for the wrong address
 * @param {Boolean} enabled
 * @param {Number} id
 * @param {String} type
 */
const switchEditAddressButtonColor = function switchEditAddressButtonColor(
  enabled,
  id,
  type,
) {
  const addressBtn = $(`#id-address-${type}-address-${id} a.edit-address`);
  const classesToToggle = ['text-info', 'address-item-invalid'];

  $(`#${type}-addresses a.edit-address`).removeClass(classesToToggle);

  addressBtn.toggleClass(classesToToggle, enabled);
};

/**
 * Enable/disable the continue address button
 */
const switchConfirmAddressesButtonState = function switchConfirmAddressesButtonState(
  enable,
) {
  $('button[name=confirm-addresses]').prop('disabled', !enable);
};
