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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import $ from 'jquery';
import prestashop from 'prestashop';

export default function () {
  const $body = $('body');
  const {checkoutNewCustomerRelatedBlock} = prestashop.selectors.checkout;
  const {checkoutLoginSuggestionBlock} = prestashop.selectors.checkout;
  const {checkoutCustomerForm} = prestashop.selectors.checkout;
  const $checkoutNewCustomerRelatedBlock = $(checkoutNewCustomerRelatedBlock);
  const $checkoutLoginSuggestionBlock = $(checkoutLoginSuggestionBlock);
  const $checkoutCustomerForm = $(checkoutCustomerForm);
  const $checkoutFormEmailInput = $checkoutCustomerForm.find('[name="email"]');
  let timeout;

  const handleBlockDisplay = (displayType = 'reset') => {
    if (displayType === 'customerExists') {
      $checkoutNewCustomerRelatedBlock.hide();
      $checkoutLoginSuggestionBlock.show();
    } else if (displayType === 'customerNotExists') {
      $checkoutNewCustomerRelatedBlock.show();
      $checkoutLoginSuggestionBlock.hide();
    } else {
      $checkoutNewCustomerRelatedBlock.hide();
      $checkoutLoginSuggestionBlock.hide();
    }
  };

  const handleRequest = (email) => {
    const url = $checkoutCustomerForm.attr('action');

    $.post(url, {
      ajax: 1,
      action: 'checkCustomerInformation',
      email,
    })
      .then((resp) => {
        if (!resp.hasError) {
          if (resp.customerExists) {
            handleBlockDisplay('customerExists');
          } else {
            handleBlockDisplay('customerNotExists');
          }
        } else {
          handleBlockDisplay();
        }
      });
  };

  const fetchForCustomerInformation = () => {
    const email = $checkoutFormEmailInput.val();

    clearTimeout(timeout);
    timeout = setTimeout(() => handleRequest(email), 300);
  };

  $body.on('keyup', $checkoutFormEmailInput, fetchForCustomerInformation);
}
