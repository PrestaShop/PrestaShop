/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
