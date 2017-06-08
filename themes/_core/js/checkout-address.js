/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery'
import prestashop from 'prestashop'

export default function () {
  $('.js-edit-addresses').on('click', (event) => {
    event.stopPropagation();
    $('#checkout-addresses-step').trigger('click');
    prestashop.emit('editAddress');
  });
  $('.js-address-selector input[type=radio]:not(:checked)').on('click', function () {
    $('button[name=confirm-addresses]').prop("disabled", "");
    if (0 < $('.js-address-error').length) {
      $('.js-address-error').hide();
      var idFailureAddress = $(".js-address-error").prop('id').split('-').pop();
      $('#id-address-delivery-address-' + idFailureAddress + ' a.edit-address').prop('style', 'color: #7a7a7a !important');
      $('#id-address-invoice-address-' + idFailureAddress + ' a.edit-address').prop('style', 'color: #7a7a7a !important');
    }
  });
}

$(document).ready(() => {
  if (0 < $('.js-address-error').length) {
    var idFailureAddress = $(".js-address-error").prop('id').split('-').pop();
    if ($(".js-address-error").attr('name').split('-').pop() == "delivery") {
      $('#id-address-delivery-address-' + idFailureAddress + ' a.edit-address').prop('style', 'color: #2fb5d2 !important');
    } else {
      $('#id-address-invoice-address-' + idFailureAddress + ' a.edit-address').prop('style', 'color: #2fb5d2 !important');
    }
    $('button[name=confirm-addresses]').prop('disabled', 'disabled');
  }
})
;
