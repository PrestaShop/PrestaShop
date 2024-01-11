// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import type ProductData from '@data/faker/product';
import type {ProductStockMovement} from '@data/types/product';

import type {Page} from 'playwright';

/**
 * Stocks tab on new product V2 page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class StocksTab extends BOBasePage {
  private readonly stocksTabLink: string;

  private readonly initialQuantitySpan: string;

  private readonly productQuantityInput: string;

  private readonly productMinimumQuantityInput: string;

  private readonly productStockLocationInput: string;

  private readonly productLowStockThresholdCheckbox: string;

  private readonly productLowStockThresholdInput: string;

  private readonly productLabelAvailableNowDropdown: string;

  private readonly productLabelAvailableNowDropdownItem: (locale: string) => string;

  private readonly productLabelAvailableNowInput: (languageId: string) => string;

  private readonly productLabelAvailableLaterDropdown: string;

  private readonly productLabelAvailableLaterDropdownItem: (locale: string) => string;

  private readonly productLabelAvailableLaterInput: (languageId: string) => string;

  private readonly productAvailableDateInput: string;

  private readonly behaviourOutOfStockInput: (id: number) => string;

  private readonly denyOrderRadioButton: string;

  private readonly allowOrderRadioButton: string;

  private readonly useDefaultBehaviourRadioButton: string;

  private readonly stockMovementsDiv: string;

  private readonly dateTimeRowInTable: (movementRow: number) => string;

  private readonly employeeRowInTable: (movementRow: number) => string;

  private readonly quantityRowInTable: (movementRow: number) => string;

  private readonly stockMovementsLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on stocks tab
   */
  constructor() {
    super();

    // Selectors in stocks tab
    this.stocksTabLink = '#product_stock-tab-nav';
    this.initialQuantitySpan = '#product_stock_quantities_delta_quantity_quantity span';
    this.productQuantityInput = '#product_stock_quantities_delta_quantity_delta';
    this.productMinimumQuantityInput = '#product_stock_quantities_minimal_quantity';
    this.productStockLocationInput = '#product_stock_options_stock_location';
    this.productLowStockThresholdCheckbox = '#product_stock_options_disabling_switch_low_stock_threshold_1';
    this.productLowStockThresholdInput = '#product_stock_options_low_stock_threshold';
    this.productLabelAvailableNowDropdown = '#product_stock_availability_available_now_label_dropdown';
    this.productLabelAvailableNowDropdownItem = (locale: string) => '#product_stock_availability_available_now_label '
      + `span.dropdown-item[data-locale="${locale}"]`;
    this.productLabelAvailableNowInput = (languageId: string) => `#product_stock_availability_available_now_label_${languageId}`;
    this.productLabelAvailableLaterDropdown = '#product_stock_availability_available_later_label_dropdown';
    this.productLabelAvailableLaterDropdownItem = (locale: string) => '#product_stock_availability_available_later_label '
      + `span.dropdown-item[data-locale="${locale}"]`;
    this.productLabelAvailableLaterInput = (languageId: string) => `#product_stock_availability_available_later_label_${
      languageId}`;
    this.productAvailableDateInput = '#product_stock_availability_available_date';
    // When out of stock selectors
    this.behaviourOutOfStockInput = (id: number) => `#product_stock_availability_out_of_stock_type_${id} +i`;
    this.denyOrderRadioButton = this.behaviourOutOfStockInput(0);
    this.allowOrderRadioButton = this.behaviourOutOfStockInput(1);
    this.useDefaultBehaviourRadioButton = this.behaviourOutOfStockInput(2);

    // Stock movement table selectors
    this.stockMovementsDiv = '#product_stock_quantities_stock_movements';
    this.dateTimeRowInTable = (movementRow: number) => `${this.stockMovementsDiv}_${movementRow}_date + span`;
    this.employeeRowInTable = (movementRow: number) => `${this.stockMovementsDiv}_${movementRow}_employee_name + span`;
    this.quantityRowInTable = (movementRow: number) => `${this.stockMovementsDiv}_${movementRow}_delta_quantity + span`;
    this.stockMovementsLink = `${this.stockMovementsDiv} + div > a`;
  }

  /*
  Methods
   */
  /**
   * Get Product quantity
   * @param page {Page} Browser tab
   */
  async getProductQuantity(page:Page): Promise<number> {
    return parseInt(await this.getTextContent(page, this.initialQuantitySpan), 10);
  }

  /**
   * Set product quantity
   * @param page {Page} Browser tab
   * @param quantity {number} Quantity value to set on quantity input
   * @returns {Promise<void>}
   */
  async setProductQuantity(page: Page, quantity: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.stocksTabLink);
    const initialQuantity = await this.getProductQuantity(page);
    await this.setQuantityDelta(page, quantity - initialQuantity);
  }

  /**
   * Set product stock
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set in stock form
   * @returns {Promise<void>}
   */
  async setProductStock(page:Page, productData: ProductData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.stocksTabLink);
    await this.setQuantityDelta(page, productData.quantity);
    await this.setValue(page, this.productMinimumQuantityInput, productData.minimumQuantity);
    await this.setStockLocation(page, productData.stockLocation);

    await this.setOptionWhenOutOfStock(page, productData.behaviourOutOfStock);

    await this.setLabelWhenInStock(page, productData.labelWhenInStock);
    await this.setLabelWhenOutOfStock(page, productData.labelWhenOutOfStock);
  }

  /**
   * Set quantity delta
   * @param page {Page} Browser tab
   * @param quantity {number} Quantity delta
   * @returns {Promise<void>}
   */
  async setQuantityDelta(page: Page, quantity: number): Promise<void> {
    await this.setValue(page, this.productQuantityInput, quantity);
  }

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
      case 'Default behavior':
      case 'Use default behavior':
        await this.setChecked(page, this.useDefaultBehaviourRadioButton);
        break;
      default:
        throw new Error(`Option ${option} was not found`);
    }
  }

  /**
   * @param page {Page}
   * @param inputName {string}
   * @param languageId {string | undefined}
   */
  async getValue(page: Page, inputName: string, languageId?: string): Promise<string> {
    switch (inputName) {
      case 'available_date':
        return this.getAttributeContent(page, this.productAvailableDateInput, 'value');
      case 'available_later':
        return this.getAttributeContent(page, this.productLabelAvailableLaterInput(languageId!), 'value');
      case 'available_now':
        return this.getAttributeContent(page, this.productLabelAvailableNowInput(languageId!), 'value');
      case 'low_stock_threshold':
        return this.getAttributeContent(page, this.productLowStockThresholdInput, 'value');
      case 'low_stock_threshold_enabled':
        return (await this.isChecked(page, this.productLowStockThresholdCheckbox)) ? '1' : '0';
      case 'minimal_quantity':
        return this.getAttributeContent(page, this.productMinimumQuantityInput, 'value');
      case 'location':
        return this.getAttributeContent(page, this.productStockLocationInput, 'value');
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }

  /**
   * Get stock movements data
   * @param page {Page} Browser tab
   * @param movementRow {number} Movement row in table stock movements
   */
  async getStockMovement(page: Page, movementRow: number): Promise<ProductStockMovement> {
    return {
      dateTime: await this.getTextContent(page, this.dateTimeRowInTable(movementRow - 1)),
      employee: await this.getTextContent(page, this.employeeRowInTable(movementRow - 1), false),
      quantity: await this.getNumberFromText(page, this.quantityRowInTable(movementRow - 1)),
    };
  }

  /**
   * Click on "View All Stock Movements" link
   * @param page {Page} Browser tab
   */
  async clickViewAllStockMovements(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.stockMovementsLink);
  }

  /**
   * Set the Minimum quantity for sale
   * @param page {Page} Browser tab
   * @param minimalQuantiy {number} Minimal Quantity
   */
  async setMinimalQuantity(page: Page, minimalQuantiy: number): Promise<void> {
    await this.setValue(page, this.productMinimumQuantityInput, minimalQuantiy);
  }

  /**
   * Set the Stock location
   * @param page {Page} Browser tab
   * @param stockLocation {number} Stock location
   */
  async setStockLocation(page: Page, stockLocation: string): Promise<void> {
    await this.setValue(page, this.productStockLocationInput, stockLocation);
  }

  /**
   * Enable/Disable the low stock alert by email
   * @param page {Page} Browser tab
   * @param statusAlert {boolean} Status
   * @param thresholdValue {number} Threshold value
   */
  async setLowStockAlertByEmail(page: Page, statusAlert: boolean, thresholdValue: number = 0): Promise<void> {
    const isLowStockAlertByEmail: boolean = (await this.getValue(page, 'low_stock_threshold_enabled') === '1');

    if (isLowStockAlertByEmail !== statusAlert) {
      await this.clickAndWaitForLoadState(page, this.productLowStockThresholdCheckbox);
    }

    // Define the threshold only if the low stock alert is enabled
    if (statusAlert) {
      await this.setValue(page, this.productLowStockThresholdInput, thresholdValue);
    }
  }

  /**
   * Set label when in stock
   * @param page {Page} Browser tab
   * @param label {string} Label to set when in stock in the input
   * @returns {Promise<void>}
   */
  async setLabelWhenInStock(page: Page, label: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.productLabelAvailableNowDropdown);
    await page
      .locator(this.productLabelAvailableNowDropdownItem('en'))
      .evaluate((el: HTMLElement) => el.click());
    await this.setValue(page, this.productLabelAvailableNowInput('1'), label);
  }

  /**
   * Set label when out of stock
   * @param page {Page} Browser tab
   * @param label {string} Label to set when out of stock in the input
   */
  async setLabelWhenOutOfStock(page: Page, label: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.productLabelAvailableLaterDropdown);
    await page
      .locator(this.productLabelAvailableLaterDropdownItem('en'))
      .evaluate((el: HTMLElement) => el.click());
    await this.setValue(page, this.productLabelAvailableLaterInput('1'), label);
  }

  /**
   * Set availability date
   * @param page {Page} Browser tab
   * @param date {string} Label to set when availability date in the input
   */
  async setAvailabilityDate(page: Page, date: string): Promise<void> {
    await this.setValue(page, this.productAvailableDateInput, date);
  }

  /**
   * Is quantity input visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isQuantityInputVisible(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.stocksTabLink);
    return this.elementVisible(page, this.productQuantityInput, 1000);
  }
}

export default new StocksTab();
