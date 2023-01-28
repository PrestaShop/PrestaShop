import type {Page} from 'playwright';

// Import page
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import ProductData from '@data/faker/product';

/**
 * Virtual product tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class VirtualProductTab extends BOBasePage {
  private readonly virtualProductTabLink: string;

  private readonly productQuantityInput: string;

  private readonly productMinimumQuantityInput: string;

  private readonly productChooseFile: string;

  private readonly productFile: string;

  private readonly productFileNameInput: string;

  private readonly productFileDownloadTimesLimit: string;

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
    this.productChooseFile = '#product_stock_virtual_product_file_has_file_1';
    this.productFile = '#product_stock_virtual_product_file_file';
    this.productFileNameInput = '#product_stock_virtual_product_file_name';
    this.productFileDownloadTimesLimit = '#product_stock_virtual_product_file_download_times_limit';
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
  async setVirtualProduct(page: Page, productData: ProductData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.virtualProductTabLink);
    await this.setValue(page, this.productQuantityInput, productData.quantity);
    await this.setValue(page, this.productMinimumQuantityInput, productData.minimumQuantity);
    if (productData.downloadFile) {
      await this.waitForSelectorAndClick(page, this.productChooseFile);
      await this.waitForVisibleSelector(page, this.productFile);
      await this.uploadFile(page, this.productFile, productData.fileName);
      await this.setValue(page, this.productFileNameInput, productData.fileName);
      await this.setValue(page, this.productFileDownloadTimesLimit, productData.allowedDownload);
    }
  }
}

export default new VirtualProductTab();
