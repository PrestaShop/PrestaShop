/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ConfirmModal from '@components/modal';
import ProductMap from '@pages/product/product-map';

export default class ProductFooterManager {
  constructor() {
    this.initFooterButton(ProductMap.footer.deleteProductButton, ProductMap.footer.deleteProductModalId);
    this.initFooterButton(ProductMap.footer.duplicateProductButton, ProductMap.footer.duplicateProductModalId);
  }

  private initFooterButton(buttonId: string, modalId: string): void {
    const $footerButton = $(buttonId);
    $footerButton.on('click', () => {
      const modal = new ConfirmModal(
        {
          id: modalId,
          confirmTitle: $footerButton.data('modal-title'),
          confirmMessage: $footerButton.data('modal-message') ?? '',
          confirmButtonLabel: $footerButton.data('modal-apply'),
          closeButtonLabel: $footerButton.data('modal-cancel'),
          confirmButtonClass: $footerButton.data('confirm-button-class'),
          closable: true,
        },
        () => {
          const buttonUrl = $footerButton.data('buttonUrl');
          $(ProductMap.productFormSubmitButton).prop('disabled', true);

          const form = document.createElement('form');
          form.setAttribute('method', 'POST');
          form.setAttribute('action', buttonUrl);
          form.setAttribute('style', 'display: none;');
          document.body.appendChild(form);
          form.submit();
        },
      );
      modal.show();
    });
  }
}
