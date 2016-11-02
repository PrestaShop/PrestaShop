/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery'
import prestashop from 'prestashop'

export default function () {
  let $body = $('body');
  let deliveryFormSelector = '#js-delivery';
  let summarySelector = '#js-checkout-summary';
  let deliveryStepSelector = '#checkout-delivery-step';
  let editDeliveryButtonSelector = '.js-edit-delivery';

  let updateDeliveryForm = () => {
    let $deliveryMethodForm = $(deliveryFormSelector);
    let requestData = $deliveryMethodForm.serialize();
    let $inputChecked = $(event.currentTarget);
    let $newDeliveryOption = $inputChecked.parents("div.delivery-option");

    $.post($deliveryMethodForm.data('url-update'), requestData).then((resp) => {
      $(summarySelector).replaceWith(resp.preview);
      prestashop.emit('updatedDeliveryForm', {dataForm: $deliveryMethodForm.serializeArray(), deliveryOption: $newDeliveryOption});
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
