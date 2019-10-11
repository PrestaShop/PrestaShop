/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import OrderViewPageMap from './OrderViewPageMap';

const $ = window.$;

$(() => {
  const DISCOUNT_TYPE_AMOUNT = 'amount';
  const DISCOUNT_TYPE_PERCENT = 'percent';
  const DISCOUNT_TYPE_FREE_SHIPPING = 'free_shipping';

  initAddCartRuleFormHandler();
  initAddProductFormHandler();

  function initAddProductFormHandler() {
    const $modal = $(OrderViewPageMap.updateOrderProductModal);

    $modal.on('click', '.js-order-product-update-btn', (event) => {
      const $btn = $(event.currentTarget);

      $modal.find('.js-update-product-name').text($btn.data('product-name'));
      $modal.find(OrderViewPageMap.updateOrderProductPriceTaxExclInput).val($btn.data('product-price-tax-excl'));
      $modal.find(OrderViewPageMap.updateOrderProductPriceTaxInclInput).val($btn.data('product-price-tax-incl'));
      $modal.find(OrderViewPageMap.updateOrderProductQuantityInput).val($btn.data('product-quantity'));
      $modal.find('form').attr('action', $btn.data('update-url'));
    });
  }

  function initAddCartRuleFormHandler() {
    const $modal = $(OrderViewPageMap.addCartRuleModal);
    const $form = $modal.find('form');
    const $valueHelp = $modal.find(OrderViewPageMap.cartRuleHelpText);
    const $invoiceSelect = $modal.find(OrderViewPageMap.addCartRuleInvoiceIdSelect);
    const $valueInput = $form.find(OrderViewPageMap.addCartRuleValueInput);
    const $valueFormGroup = $valueInput.closest('.form-group');

    $form.find(OrderViewPageMap.addCartRuleApplyOnAllInvoicesCheckbox).on('change', (event) => {
      const isChecked = $(event.currentTarget).is(':checked');

      $invoiceSelect.attr('disabled', isChecked);
    });

    $form.find(OrderViewPageMap.addCartRuleTypeSelect).on('change', (event) => {
      const selectedCartRuleType = $(event.currentTarget).val();

      if (selectedCartRuleType === DISCOUNT_TYPE_AMOUNT) {
        $valueHelp.removeClass('d-none');
      } else {
        $valueHelp.addClass('d-none');
      }

      if (selectedCartRuleType === DISCOUNT_TYPE_FREE_SHIPPING) {
        $valueFormGroup.addClass('d-none');
        $valueInput.attr('disabled', true);
      } else {
        $valueFormGroup.removeClass('d-none');
        $valueInput.attr('disabled', false);
      }
    });
  }
});
