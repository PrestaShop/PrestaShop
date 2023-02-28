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

import ProductMap from '@pages/product/product-map';

const VirtualProductMap = ProductMap.virtualProduct;

export default class VirtualProductManager {
  productFormModel: Record<string, any>;

  constructor(productFormModel: Record<string, any>) {
    this.productFormModel = productFormModel;

    this.init();
  }

  /**
   * @private
   */
  private init(): void {
    this.productFormModel.watch('stock.hasVirtualProductFile', () => this.toggleContentVisibility());
    this.toggleContentVisibility();
    this.listenFileUpload();
  }

  /**
   * Shows/hides file form content depending if "Has file" switch is on or off.
   */
  private toggleContentVisibility(): void {
    document.querySelector(VirtualProductMap.fileContentContainer)?.classList.toggle(
      'd-none',
      Number(this.productFormModel.getProduct().stock.hasVirtualProductFile) !== 1,
    );
  }

  /**
   * Prefills file display name input with filename value on upload
   */
  private listenFileUpload(): void {
    const fileUploadInput = <HTMLInputElement> document.querySelector(VirtualProductMap.fileUploadInput);
    fileUploadInput.addEventListener('change', (e: Event) => {
      const fileInput = <HTMLInputElement> e.currentTarget;
      const selectedFile = <File|null> fileInput?.files?.[0];

      if (selectedFile) {
        const filenameInput = <HTMLInputElement> document.querySelector(VirtualProductMap.filenameInput);
        filenameInput.value = selectedFile.name;
      }
    });
  }
}
