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
import Router from '@components/router';
import ProductMap from '@pages/product/product-map';
import {FormIframeModal} from '@components/modal';

export default class CreateProductModal {
  private router: Router;

  constructor() {
    this.router = new Router();
    this.init();
  }

  private init(): void {
    $(ProductMap.create.newProductButton).on('click', (event: JQuery.ClickEvent) => {
      event.preventDefault();
      const $link = $(event.target);
      const linkUrl = `${$link.prop('href')}&liteDisplaying=1`;

      const iframeModal = new FormIframeModal({
        id: 'modal-create-product',
        formSelector: 'form[name="product"]',
        formUrl: linkUrl,
        closable: true,
        // We override the body selector so that the modal keeps the size of the initial create form even after submit
        autoSizeContainer: '.create-product-form',
        onFormLoaded: (form: HTMLElement, formData: FormData, dataAttributes: DOMStringMap | null): void => {
          if (dataAttributes) {
            if (dataAttributes.modalTitle) {
              iframeModal.setTitle(dataAttributes.modalTitle);
            }

            if (dataAttributes.productId) {
              const editUrl = this.router.generate('admin_products_v2_edit', {productId: dataAttributes.productId});
              // Keep showing loading until the page is refreshed
              iframeModal.showLoading();
              window.location.href = editUrl;
            }
          }
        },
      });
      iframeModal.show();
    });
  }
}
