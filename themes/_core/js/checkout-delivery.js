/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

  const updateDeliveryForm = (event) => {
    const $deliveryMethodForm = $(deliveryFormSelector);
    const requestData = $deliveryMethodForm.serialize();
    const $inputChecked = $(event.currentTarget);
    const $newDeliveryOption = $inputChecked.parents(prestashop.selectors.checkout.deliveryOption);

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
      });
  };

  $body.on('change', `${deliveryFormSelector} input`, updateDeliveryForm);

  $body.on('click', editDeliveryButtonSelector, (event) => {
    event.stopPropagation();
    $(deliveryStepSelector).trigger('click');
    prestashop.emit('editDelivery');
  });
}
