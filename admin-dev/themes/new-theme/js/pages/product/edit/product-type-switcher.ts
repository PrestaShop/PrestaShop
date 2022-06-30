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

import {ConfirmModal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import ProductConst from '@pages/product/constants';
import ProductTypeSelector from '@pages/product/create/product-type-selector';

/**
 * This component watches for modification of the product type, when it happens it displays a modal warning
 * for the user with a warning about what is going to be deleted if he validates this change. If modification
 * is confirmed the form is submitted right away to validate the change and update the page.
 */
export default class ProductTypeSwitcher {
  private $typeSelector: JQuery;

  private $productForm: JQuery;

  private $modalContent: JQuery;

  private $productTypePreview: JQuery;

  private readonly productId: number;

  private readonly initialType: string;

  /**
   * @param {JQuery} $productForm Product form that needs to be submitted
   */
  constructor($productForm: JQuery) {
    this.$productForm = $productForm;
    this.$typeSelector = $(ProductMap.productType.headerSelector);
    this.$modalContent = $(ProductMap.productType.switchModalContent);
    this.$productTypePreview = $(ProductMap.productType.headerPreviewButton);

    this.productId = parseInt($productForm.data('productId'), 10);
    this.initialType = <string> this.$typeSelector.val();

    this.$productTypePreview.on('click', () => this.showSelectionModal());
  }

  private showSelectionModal() {
    const selectionModal = new ConfirmModal(
      {
        id: ProductMap.productType.switchModalId,
        confirmMessage: this.$modalContent.html(),
        modalTitle: this.$typeSelector.data('switch-modal-title'),
        confirmButtonLabel: this.$typeSelector.data('modal-apply'),
        closeButtonLabel: this.$typeSelector.data('modal-cancel'),
        closable: true,
      },
      () => {
        // On selection confirm we display a confirmation modal with a warning message
        const modalSelector = $(ProductMap.productType.switchModalSelector);
        this.confirmTypeSubmit(<string> modalSelector.val());
      },
    );

    // We init the type selector component but we target the one which has been rendered inside the modal
    new ProductTypeSelector(ProductMap.productType.switchModalSelector, this.initialType);

    // Confirm button is disabled until a different type is selected
    const $switchModalButton: JQuery = $(ProductMap.productType.switchModalButton);
    $switchModalButton.prop('disabled', true);
    $(ProductMap.productType.switchModalSelector).on('change', () => {
      const modalSelector = $(ProductMap.productType.switchModalSelector);
      $switchModalButton.prop('disabled', <string> modalSelector.val() === this.initialType);
    });

    selectionModal.show();
  }

  private confirmTypeSubmit(newType: string) {
    const stockEnabled: boolean = this.$typeSelector.data('stockEnabled');
    const ecotaxEnabled: boolean = this.$typeSelector.data('ecotaxEnabled');
    const confirmWarnings = [];

    // eslint-disable-next-line default-case
    switch (this.initialType) {
      case ProductConst.PRODUCT_TYPE.COMBINATIONS:
        confirmWarnings.push(this.$typeSelector.data('combinationsWarning'));
        break;
      case ProductConst.PRODUCT_TYPE.PACK:
        confirmWarnings.push(this.$typeSelector.data('packWarning'));
        break;
      case ProductConst.PRODUCT_TYPE.VIRTUAL:
        confirmWarnings.push(this.$typeSelector.data('virtualWarning'));
        break;
    }

    // Switching FROM and TO combination resets the stock
    if (stockEnabled
      && (newType === ProductConst.PRODUCT_TYPE.COMBINATIONS || this.initialType === ProductConst.PRODUCT_TYPE.COMBINATIONS)
    ) {
      confirmWarnings.push(this.$typeSelector.data('stockWarning'));
    }

    // When switching to virtual the ecotax is reset
    if (ecotaxEnabled && newType === ProductConst.PRODUCT_TYPE.VIRTUAL) {
      confirmWarnings.push(this.$typeSelector.data('ecotaxWarning'));
    }

    let confirmMessage: string = `<div class="alert alert-info">${this.$typeSelector.data('confirm-message')}</div>`;

    // Add warnings to confirmation message
    if (confirmWarnings && confirmWarnings.length > 0) {
      confirmWarnings.forEach((confirmWarning: string) => {
        confirmMessage += `<div class="alert alert-warning">${confirmWarning}</div>`;
      });
    }

    const modal = new ConfirmModal(
      {
        id: 'modal-confirm-product-type',
        confirmTitle: this.$typeSelector.data('modal-title'),
        confirmMessage: `${confirmMessage}`,
        confirmButtonLabel: this.$typeSelector.data('modal-apply'),
        closeButtonLabel: this.$typeSelector.data('modal-cancel'),
        closable: false,
      },
      () => {
        $(ProductMap.productFormSubmitButton).prop('disabled', true);
        this.$typeSelector.val(newType);
        this.$productForm.submit();
      },
    );
    modal.show();
  }
}
