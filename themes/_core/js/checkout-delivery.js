/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import $ from 'jquery';
import prestashop from 'prestashop';
import {refreshCheckoutPage} from './common';

export default function () {
  const $body = $('body');
  const {deliveryFormSelector} = prestashop.selectors.checkout;
  const {summarySelector} = prestashop.selectors.checkout;
  const {deliveryStepSelector} = prestashop.selectors.checkout;
  const {editDeliveryButtonSelector} = prestashop.selectors.checkout;
  let pendingUpdates = 0;

  const updateDeliveryForm = (event) => {
    const $deliveryMethodForm = $(deliveryFormSelector);
    const requestData = $deliveryMethodForm.serialize();
    const $inputChecked = $(event.currentTarget);
    const $newDeliveryOption = $inputChecked.parents(prestashop.selectors.checkout.deliveryOption);
    // A carrier update saved after "Continue" marks this step incomplete again and sends the customer back to it
    const $confirmButton = $deliveryMethodForm.find('button[name="confirmDeliveryOption"]');

    pendingUpdates += 1;
    $confirmButton.prop('disabled', true);

    $.post($deliveryMethodForm.data('url-update'), requestData)
      .then((resp) => {
        $(summarySelector).replaceWith(resp.preview);

        if ($(prestashop.selectors.checkout.cartPaymentStepRefresh).length) {
          // we get the refresh flag : on payment step we need to refresh page to be sure
          // amount is correctly updated on payment modules
          refreshCheckoutPage();
        }

        prestashop.emit('updatedDeliveryForm', {
          dataForm: $deliveryMethodForm.serializeArray(),
          deliveryOption: $newDeliveryOption,
          resp,
        });
      })
      .fail((resp) => {
        prestashop.trigger('handleError', {
          eventType: 'updateDeliveryOptions',
          resp,
        });
      })
      .always(() => {
        pendingUpdates -= 1;
        if (pendingUpdates === 0) {
          $confirmButton.prop('disabled', false);
        }
      });
  };

  $body.on('change', `${deliveryFormSelector} input`, updateDeliveryForm);

  $body.on('click', editDeliveryButtonSelector, (event) => {
    event.stopPropagation();
    $(deliveryStepSelector).trigger('click');
    prestashop.emit('editDelivery');
  });
}
