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

  private readonly productFileSection: string;

  private readonly productChooseFile: (toCheck: number) => string;

  private readonly productFile: string;

  private readonly errorMessageInFileInput: string;

  private readonly productFileNameInput: string;

  private readonly productFileDownloadTimesLimit: string;

  private readonly productFileExpirationDate: string;

  private readonly productFileNumberOfDays: string;

  private readonly denyOrderRadioButton: string;

  private readonly allowOrderRadioButton: string;

  private readonly useDefaultBehaviourRadioButton: string;

  private readonly editDefaultBehaviourLink: string;

  private readonly labelWhenInStock: string;

  private readonly labelWhenOutOfStock: string;

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
    this.productFileSection = '#product_stock_virtual_product_file';
    this.productChooseFile = (toCheck: number) => `#product_stock_virtual_product_file_has_file_${toCheck}`;
    this.productFile = '#product_stock_virtual_product_file_file';
    this.errorMessageInFileInput = `${this.productFileSection} div.form-group.file-widget.has-error div.alert-danger p`;
    this.productFileNameInput = '#product_stock_virtual_product_file_name';
    this.productFileDownloadTimesLimit = '#product_stock_virtual_product_file_download_times_limit';
    this.productFileExpirationDate = '#product_stock_virtual_product_file_expiration_date';
    this.productFileNumberOfDays = '#product_stock_virtual_product_file_access_days_limit';

    // When out of stock selectors
    this.denyOrderRadioButton = '#product_stock_availability_out_of_stock_type_0 +i';
    this.allowOrderRadioButton = '#product_stock_availability_out_of_stock_type_1 +i';
    this.useDefaultBehaviourRadioButton = '#product_stock_availability_out_of_stock_type_2 +i';
    this.editDefaultBehaviourLink = '#product_stock_availability a[href*=configuration_fieldset_stock]';
    this.labelWhenInStock = '#product_stock_availability_available_now_label_1';
    this.labelWhenOutOfStock = '#product_stock_availability_available_later_label_1';
  }

  /*
  Methods
   */

  /**
   * Set product quantity
   * @param page {Page} Browser tab
   * @param quantity {string} Product quantity to set in the input
   * @returns {Promise<void>}
   */
  async setProductQuantity(page: Page, quantity: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.virtualProductTabLink);
    await this.setValue(page, this.productQuantityInput, quantity);
  }

  /**
   * Set virtual product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in virtual product form
   * @returns {Promise<void>}
   */
  async setVirtualProduct(page: Page, productData: ProductData): Promise<void> {
    await this.setProductQuantity(page, productData.quantity);
    await this.setValue(page, this.productMinimumQuantityInput, productData.minimumQuantity);
    if (productData.downloadFile) {
      await this.setChecked(page, this.productChooseFile(productData.downloadFile ? 1 : 0));
      await this.waitForVisibleSelector(page, this.productFile);
      await this.uploadFile(page, this.productFile, productData.fileName);
      await this.setValue(page, this.productFileNameInput, productData.fileName);
      await this.setValue(page, this.productFileDownloadTimesLimit, productData.allowedDownload);
      await this.setValue(page, this.productFileExpirationDate, productData.expirationDate!);
      await this.setValue(page, this.productFileNumberOfDays, productData.numberOfDays!);
    }
  }

  /**
   * Get error message in downloaded file input
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getErrorMessageInDownloadFileInput(page: Page): Promise<string> {
    return this.getTextContent(page, this.errorMessageInFileInput);
  }

  // Methods for when out of stock
  /**
   * Set option when out of stock
   * @param page {Page} Browser tab
   * @param option {string} Option to check
   * @returns {Promise<void>}
   */
  async setOptionWhenOutOfStock(page: Page, option: string): Promise<void> {
    switch (option) {
      case 'Deny orders':
        await this.setChecked(page, this.denyOrderRadioButton);
        break;
      case 'Allow orders':
        await this.setChecked(page, this.allowOrderRadioButton);
        break;
      case 'Use default behavior':
        await this.setChecked(page, this.useDefaultBehaviourRadioButton);
        break;
      default:
        throw new Error(`Option ${option} was not found`);
    }
  }

  /**
   * Click on edit default behaviour link
   * @param page {Page} Browser tab
   * @returns {Promise<Page>}
   */
  async clickOnEditDefaultBehaviourLink(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.editDefaultBehaviourLink);
  }

  /**
   * Set label when in stock
   * @param page {Page} Browser tab
   * @param label {string} Label to set when in stock in the input
   * @returns {Promise<void>}
   */
  async setLabelWhenInStock(page: Page, label: string): Promise<void> {
    await this.setValue(page, this.labelWhenInStock, label);
  }

  /**
   * Set label when out of stock
   * @param page {Page} Browser tab
   * @param label {string} Label to set when out of stock in the input
   */
  async setLabelWhenOutOfStock(page: Page, label: string): Promise<void> {
    await this.setValue(page, this.labelWhenOutOfStock, label);
  }
}

export default new VirtualProductTab();
