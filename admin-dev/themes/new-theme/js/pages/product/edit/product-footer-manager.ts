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

export default class ProductFooterManager {
  $deleteProductButton: JQuery;

  constructor() {
    this.$deleteProductButton = $(ProductMap.footer.deleteProductButton);
    this.$deleteProductButton.click(() => this.deleteProduct());
  }

  private deleteProduct(): void {
    const modal = new ConfirmModal(
      {
        id: 'modal-confirm-delete-product',
        confirmTitle: this.$deleteProductButton.data('modal-title'),
        confirmMessage: this.$deleteProductButton.data('modal-message'),
        confirmButtonLabel: this.$deleteProductButton.data('modal-apply'),
        closeButtonLabel: this.$deleteProductButton.data('modal-cancel'),
        confirmButtonClass: 'btn-danger',
        closable: true,
      },
      () => {
        const removeUrl = this.$deleteProductButton.data('removeUrl');
        $(ProductMap.productFormSubmitButton).prop('disabled', true);

        const form = document.createElement('form');
        form.setAttribute('method', 'POST');
        form.setAttribute('action', removeUrl);
        form.setAttribute('style', 'display: none;');
        document.body.appendChild(form);
        form.submit();
      },
    );
    modal.show();
  }
}
