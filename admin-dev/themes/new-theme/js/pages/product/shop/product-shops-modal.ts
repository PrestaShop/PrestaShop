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
import ProductEventMap from '@pages/product/product-event-map';
import {FormIframeModal} from '@components/modal';
import IframeEvent from '@components/modal/iframe-event';

/**
 * This component is bound to any Create product button (via a class selector). On button click
 * it opens an iframe modal, in the iframe the shop selection page is displayed and allows you to
 * add/remove some shops to the product and then submit the modification.
 *
 * When the shop selection form page is submitted this component catches the refresh event, and
 * checks if the modification has succeeded based on the form data attributes. On success the page
 * is refreshed to show the updated product form.
 */
export default class ProductShopsModal {
  private router: Router;

  constructor() {
    this.router = new Router();
    this.init();
  }

  private init(): void {
    document.querySelectorAll<HTMLElement>(ProductMap.shops.modalButtons).forEach((button: HTMLElement) => {
      button.addEventListener('click', (event: MouseEvent) => {
        event.preventDefault();
        const formUrl = `${button.getAttribute('href')}&liteDisplaying=1`;
        this.openCreationModal(formUrl, button.innerText);
      });
    });
  }

  private openCreationModal(formUrl: string, modalTitle: string): void {
    const iframeModal = new FormIframeModal({
      id: ProductMap.shops.modalId,
      formSelector: ProductMap.shops.form,
      formUrl,
      closable: true,
      modalTitle,
      // We override the body selector so that the modal keeps the size of the initial create form even after submit (success notifications
      // are not computed in the size)
      autoSizeContainer: ProductMap.shops.modalSizeContainer,
      onFormLoaded: (form: HTMLElement, formData: FormData, dataAttributes: DOMStringMap | null): void => {
        if (dataAttributes) {
          const successAlertsCount = Number(dataAttributes.alertsSuccess);

          if (successAlertsCount) {
            const editUrl = this.router.generate('admin_products_edit', {productId: dataAttributes.productId});
            // Keep showing loading until the page is refreshed
            iframeModal.showLoading();
            window.location.href = editUrl;
          }
        }
      },
      onIframeEvent: (event: IframeEvent) => {
        if (event.name === ProductEventMap.cancelProductShops) {
          iframeModal.hide();
        }
      },
    });
    iframeModal.show();
  }
}
