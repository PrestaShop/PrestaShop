require('module-alias/register');
// Importing page
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Virtual product tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class VirtualProductTab extends BOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on Virtual product tab
   */
  constructor() {
    super();

    // Selectors in virtual product tab
    this.virtualProductTabLink = '#product_stock-tab-nav';
    this.productQuantityInput = '#product_stock_quantities_delta_quantity_delta';
    this.productMinimumQuantityInput = '#product_stock_quantities_minimal_quantity';
  }

  /*
  Methods
   */

  /**
   * Set virtual product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in virtual product form
   * @returns {Promise<void>}
   */
  async setVirtualProduct(page, productData) {
    await this.waitForSelectorAndClick(page, this.virtualProductTabLink);
    await this.setValue(page, this.productQuantityInput, productData.quantity);
    await this.setValue(page, this.productMinimumQuantityInput, productData.minimumQuantity);
    if (productData.downloadFile) {
      await this.waitForSelectorAndClick(page, '#product_stock_virtual_product_file_has_file_1');
      await this.waitForVisibleSelector(page, '#product_stock_virtual_product_file_file');
      await this.uploadFile(page, '#product_stock_virtual_product_file_file', productData.fileName);
      await this.setValue(page, '#product_stock_virtual_product_file_name', productData.fileName);
      await this.setValue(page, '#product_stock_virtual_product_file_download_times_limit', productData.allowedDownload);
    }
  }
}

module.exports = new VirtualProductTab();
