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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import Router from '@components/router';
import ProductMap from '@pages/product/product-map';
import {FormIframeModal} from '@components/modal';

/**
 * This component is bound to any Create product button (via a class selector). On button click
 * it opens an iframe modal, in the iframe the create product page is displayed, allowing you to
 * select the product type (and maybe initial shop) and then you can submit the form.
 *
 * Once the creation succeeds, the edition form is displayed, at this moment this component catches
 * the refresh event from the iframe and gets the product ID from the edit form data attributes.
 *
 * Finally, it opens the edition page of the created product in the current tab.
 */
export default class CreateProductModal {
  private router: Router;

  constructor() {
    this.router = new Router();
    this.init();
  }

  private init(): void {
    document.querySelectorAll<HTMLElement>(ProductMap.create.newProductButton).forEach((button: HTMLElement) => {
      button.addEventListener('click', (event: MouseEvent) => {
        if (button.getAttribute('target') !== '_blank') {
          event.preventDefault();
          const formUrl = `${button.getAttribute('href')}&liteDisplaying=1`;
          this.openCreationModal(formUrl);
        }
      });
    });
  }

  private openCreationModal(formUrl: string): void {
    const iframeModal = new FormIframeModal({
      id: ProductMap.create.modalId,
      // We use the edit form selector because it's the one we need to get data attributes from
      formSelector: ProductMap.productForm,
      formUrl,
      closable: true,
      // We override the body selector so that the modal keeps the size of the initial create form even after submit (success notifications
      // are not computed in the size)
      autoSizeContainer: ProductMap.create.modalSizeContainer,
      onFormLoaded: (form: HTMLElement, formData: FormData, dataAttributes: DOMStringMap | null): void => {
        if (dataAttributes) {
          if (dataAttributes.modalTitle) {
            iframeModal.setTitle(dataAttributes.modalTitle);
          }

          if (dataAttributes.productId) {
            // The parameter forceDefaultActive must be passed down in the redirect URL or it will be ignored, it was passed via data attributes
            // for this purpose only
            const editUrl = this.router.generate('admin_products_edit', {
              productId: dataAttributes.productId,
              forceDefaultActive: parseInt(dataAttributes.forceDefaultActive ?? '0', 10) === 1 ? 1 : 0,
            });

            // Keep showing loading until the page is refreshed
            iframeModal.showLoading();
            window.location.href = editUrl;
          }
        }
      },
    });
    iframeModal.show();
  }
}
