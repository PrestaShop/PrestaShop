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

import ConfirmModal from '@components/modal';
import ProductMap from '@pages/product/product-map';

/**
 * This component watches for modification of the product type, when it happens it displays a modal warning
 * for the user with a warning about what is going to be deleted if he validates this change. If modification
 * is confirmed the form is submitted right away to validate the change and update the page.
 */
export default class ProductTypeManager {
  /**
   * @param {jQuery} $typeSelector Select element to choose the product type
   * @param {jQuery} $productForm Product form that needs to be submitted
   */
  constructor($typeSelector, $productForm) {
    this.$typeSelector = $typeSelector;
    this.$productForm = $productForm;
    this.productId = parseInt($productForm.data('productId'), 10);
    this.initialType = $typeSelector.val();

    this.$typeSelector.on('change', (event) => this.confirmTypeSubmit(event));

    return {};
  }

  /**
   * @private
   */
  confirmTypeSubmit() {
    let confirmMessage = this.$typeSelector.data('confirm-message');
    let confirmWarning = '';

    // If no productId we are in creation page so no need for extra warning
    if (this.productId) {
      switch (this.initialType) {
        case ProductMap.productType.COMBINATIONS:
          confirmWarning = this.$typeSelector.data('combinations-warning');
          break;
        case ProductMap.productType.PACK:
          confirmWarning = this.$typeSelector.data('pack-warning');
          break;
        case ProductMap.productType.VIRTUAL:
          confirmWarning = this.$typeSelector.data('virtual-warning');
          break;
        case ProductMap.productType.STANDARD:
        default:
          confirmWarning = '';
          break;
      }
    }

    if (confirmWarning) {
      confirmWarning = `<div class="alert alert-warning">${confirmWarning}</div>`;
    }
    confirmMessage = `<div class="alert alert-info">${confirmMessage}</div>`;

    const modal = new ConfirmModal(
      {
        id: 'modal-confirm-product-type',
        confirmTitle: this.$typeSelector.data('modal-title'),
        confirmMessage: `${confirmMessage} ${confirmWarning}`,
        confirmButtonLabel: this.$typeSelector.data('modal-apply'),
        closeButtonLabel: this.$typeSelector.data('modal-cancel'),
        closable: false,
      },
      () => {
        $(ProductMap.productFormSubmitButton).prop('disabled', true);
        this.$productForm.submit();
      },
      () => {
        this.$typeSelector.val(this.initialType);
      },
    );
    modal.show();
  }
}
