/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery'
import prestashop from 'prestashop'
import { refreshCheckoutPage } from './common';

export default function () {
  let $body = $('body');
  let deliveryFormSelector = '#js-delivery';
  let summarySelector = '#js-checkout-summary';
  let deliveryStepSelector = '#checkout-delivery-step';
  let editDeliveryButtonSelector = '.js-edit-delivery';

  let updateDeliveryForm = (event) => {
    let $deliveryMethodForm = $(deliveryFormSelector);
    let requestData = $deliveryMethodForm.serialize();
    let $inputChecked = $(event.currentTarget);
    let $newDeliveryOption = $inputChecked.parents("div.delivery-option");

    $.post($deliveryMethodForm.data('url-update'), requestData).then((resp) => {
      $(summarySelector).replaceWith(resp.preview);


      if ($('.js-cart-payment-step-refresh').length) {
        // we get the refresh flag : on payment step we need to refresh page to be sure
        // amount is correctly updated on payment modules
        refreshCheckoutPage();
      }

      prestashop.emit('updatedDeliveryForm', {
        dataForm: $deliveryMethodForm.serializeArray(),
        deliveryOption: $newDeliveryOption,
        resp: resp
      });
    }).fail((resp) => {
      prestashop.trigger('handleError', {eventType: 'updateDeliveryOptions', resp: resp})
    });
  };

  $body.on('change', deliveryFormSelector + ' input', updateDeliveryForm);

  $body.on('click', editDeliveryButtonSelector, (event) => {
    event.stopPropagation();
    $(deliveryStepSelector).trigger('click');
    prestashop.emit('editDelivery');
  });
}
