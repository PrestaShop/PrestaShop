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

const {$} = window;

export default class VirtualProductManager {
  productFormModel: Record<string, any>;

  $virtualProductContainer: JQuery;

  $fileContentContainer: JQuery;

  constructor(productFormModel: Record<string, any>) {
    this.productFormModel = productFormModel;
    this.$virtualProductContainer = $(ProductMap.virtualProduct.container);
    this.$fileContentContainer = $(ProductMap.virtualProduct.fileContentContainer);

    this.init();
  }

  /**
   * @private
   */
  private init(): void {
    this.productFormModel.watch('stock.hasVirtualProductFile', () => this.toggleContentVisibility());
    this.toggleContentVisibility();
    this.fillVirtualProductNameField();
  }

  private toggleContentVisibility(): void {
    const hasVirtualFile = Number(this.productFormModel.getProduct().stock.hasVirtualProductFile) === 1;
    const hasErrors = this.$virtualProductContainer
      .find(ProductMap.invalidField)
      .length !== 0;

    if (hasVirtualFile || hasErrors) {
      this.showContent();
    } else {
      this.hideContent();
    }
  }

  /**
   * @private
   */
  private hideContent(): void {
    this.$fileContentContainer.addClass('d-none');
  }

  /**
   * @private
   */
  private showContent(): void {
    this.$fileContentContainer.removeClass('d-none');
  }

  private fillVirtualProductNameField(): void {
    $(ProductMap.virtualProduct.fileUploadField).on('change', () => {
      const fullPath = $(ProductMap.virtualProduct.fileUploadField).val()!;

      if (Array.isArray(fullPath)) {
          // Handle the error with the most appropriate way, here an error is just thrown
          throw new Error('Full path of the file is not supposed to be an array');
      } else {
          // Convert to string in any other case
          fullPath = fullPath.toString();
      }

      // get file name from full path
      const startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
      let filename = fullPath.substring(startIndex);

      if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
        filename = filename.substring(1);
      }
      $(ProductMap.virtualProduct.fileNameField).val(filename);
    });
  }
}
