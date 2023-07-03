import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Product settings page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ProductSettings extends BOBasePage {
  public readonly pageTitle: string;

  private readonly catalogModeToggleInput: (toggle: number) => string;

  private readonly showPricesToggleInput: (toggle: number) => string;

  private readonly maxSizeShortDescriptionInput: string;

  private readonly newDaysNumberInput: string;

  private readonly forceUpdateFriendlyUrlToggleInput: (toggle: number) => string;

  private readonly quantityDiscountBasedOnSelect: string;

  private readonly defaultActivationStatusToggleInput: (toggle: number) => string;

  private readonly saveProductGeneralFormButton: string;

  private readonly displayAvailableQuantitiesToggleInput: (toggle: number) => string;

  private readonly separatorAttributeOnProductPageSelect: string;

  private readonly displayDiscountedPriceToggleInput: (toggle: number) => string;

  private readonly saveProductPageFormButton: string;

  private readonly productsStockForm: string;

  private readonly allowOrderingOosToggleInput: (toggle: number) => string;

  private readonly enableStockManagementToggleInput: (toggle: number) => string;

  private readonly nameLangButton: string;

  private readonly nameLangSpan: (lang: string) => string;

  private readonly labelInStock: (idLang: number) => string;

  private readonly deliveryTimeInStockInput: string;

  private readonly deliveryTimeOutOfStockInput: string;

  private readonly oosAllowedBackordersLabel: (idLang: number) => string;

  private readonly oosDeniedBackordersLabel: (idLang: number) => string;

  private readonly remainingQuantityInput: string;

  private readonly displayUnavailableAttributesToggleInput: (toggle: number) => string;

  private readonly defaultPackStockManagementSelect: string;

  private readonly saveProductsStockFormButton: string;

  private readonly productsPerPageInput: string;

  private readonly productsDefaultOrderBySelect: string;

  private readonly productsDefaultOrderMethodSelect: string;

  private readonly savePaginationFormButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on add product settings page
   */
  constructor() {
    super();

    this.pageTitle = 'Product Settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // Products general form
    this.catalogModeToggleInput = (toggle: number) => `#general_catalog_mode_${toggle}`;
    this.showPricesToggleInput = (toggle: number) => `#general_catalog_mode_with_prices_${toggle}`;
    this.maxSizeShortDescriptionInput = '#general_short_description_limit';
    this.newDaysNumberInput = '#general_new_days_number';
    this.forceUpdateFriendlyUrlToggleInput = (toggle: number) => `#general_force_friendly_url_${toggle}`;
    this.quantityDiscountBasedOnSelect = '#general_quantity_discount';
    this.defaultActivationStatusToggleInput = (toggle: number) => `#general_default_status_${toggle}`;
    this.saveProductGeneralFormButton = '#form-general-save-button';

    // Product page form
    this.displayAvailableQuantitiesToggleInput = (toggle: number) => `#page_display_quantities_${toggle}`;
    this.separatorAttributeOnProductPageSelect = '#page_attribute_anchor_separator';
    this.displayDiscountedPriceToggleInput = (toggle: number) => `#page_display_discount_price_${toggle}`;
    this.saveProductPageFormButton = '#form-page-save-button';

    // Products stock form
    this.productsStockForm = '#configuration_fieldset_stock';
    this.allowOrderingOosToggleInput = (toggle: number) => `#stock_allow_ordering_oos_${toggle}`;
    this.enableStockManagementToggleInput = (toggle: number) => `#stock_stock_management_${toggle}`;
    this.nameLangButton = '#stock_in_stock_label_dropdown';
    this.nameLangSpan = (lang: string) => 'div.dropdown-menu[aria-labelledby=\'stock_in_stock_label_dropdown\']'
      + ` span[data-locale='${lang}']`;
    this.labelInStock = (idLang: number) => `#stock_in_stock_label_${idLang}`;
    this.deliveryTimeInStockInput = '#stock_delivery_time_1';
    this.deliveryTimeOutOfStockInput = '#stock_oos_delivery_time_1';
    this.oosAllowedBackordersLabel = (idLang: number) => `#stock_oos_allowed_backorders_${idLang}`;
    this.oosDeniedBackordersLabel = (idLang: number) => `#stock_oos_denied_backorders_${idLang}`;
    this.remainingQuantityInput = '#stock_display_last_quantities';
    this.displayUnavailableAttributesToggleInput = (toggle: number) => `#stock_display_unavailable_attributes_${toggle}`;
    this.defaultPackStockManagementSelect = '#stock_pack_stock_management';
    this.saveProductsStockFormButton = '#form-stock-save-button';

    // Pagination form
    this.productsPerPageInput = '#pagination_products_per_page';
    this.productsDefaultOrderBySelect = '#pagination_default_order_by';
    this.productsDefaultOrderMethodSelect = '#pagination_default_order_way';
    this.savePaginationFormButton = '#form-pagination-save-button';
  }

  /*
    Methods
  */

  /**
   * Enable/disable catalog mode
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable catalog mode status
   * @return {Promise<string>}
   */
  async changeCatalogModeStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.catalogModeToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveProductGeneralFormButton);
    await this.elementNotVisible(page, this.catalogModeToggleInput(!toEnable ? 1 : 0), 2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/disable show prices
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable show prices status
   * @return {Promise<string>}
   */
  async setShowPricesStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.showPricesToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveProductGeneralFormButton);
    await this.elementNotVisible(page, this.showPricesToggleInput(!toEnable ? 1 : 0), 2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Update number of days
   * @param page {Page} Browser tab
   * @param numberOfDays {number} Value to set on number of days input
   * @returns {Promise<string>}
   */
  async updateNumberOfDays(page: Page, numberOfDays: number): Promise<string> {
    await this.setValue(page, this.newDaysNumberInput, numberOfDays.toString());
    await page.click(this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Update max size of short description
   * @param page {Page} Browser tab
   * @param size {number} Value to set on size input
   * @returns {Promise<string>}
   */
  async UpdateMaxSizeOfSummary(page: Page, size: number): Promise<string> {
    await this.setValue(page, this.maxSizeShortDescriptionInput, size.toString());
    await page.click(this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable force update of friendly URL
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable force update friendly url status
   * @returns {Promise<string>}
   */
  async setForceUpdateFriendlyURLStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.forceUpdateFriendlyUrlToggleInput(toEnable ? 1 : 0));
    await page.click(this.saveProductGeneralFormButton);
    await this.elementNotVisible(page, this.forceUpdateFriendlyUrlToggleInput(!toEnable ? 1 : 0), 2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change default activation status
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable default activation status
   * @returns {Promise<string>}
   */
  async setDefaultActivationStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.defaultActivationStatusToggleInput(toEnable ? 1 : 0));
    await page.click(this.saveProductGeneralFormButton);
    await this.elementNotVisible(page, this.defaultActivationStatusToggleInput(!toEnable ? 1 : 0), 2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Choose quantity discounts based on
   * @param page {Page} Browser tab
   * @param basedOn {string} Value of quantity discount based on
   * @returns {Promise<string>}
   */
  async chooseQuantityDiscountsBasedOn(page: Page, basedOn: string): Promise<string> {
    await this.selectByVisibleText(page, this.quantityDiscountBasedOnSelect, basedOn);
    await page.click(this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/ Disable display available quantities
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to display available quantity
   * @returns {Promise<string>}
   */
  async setDisplayAvailableQuantitiesStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.closeAlertBlock(page);
    await this.setChecked(page, this.displayAvailableQuantitiesToggleInput(toEnable ? 1 : 0));
    await page.click(this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display remaining quantities
   * @param page {Page} Browser tab
   * @param quantity {number} Value of remaining quantity to set
   * @returns {Promise<string>}
   */
  async setDisplayRemainingQuantities(page: Page, quantity: number): Promise<string> {
    await this.closeAlertBlock(page);
    await this.setValue(page, this.remainingQuantityInput, quantity.toString());
    await page.click(this.saveProductsStockFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display unavailable product attributes
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable unavailable attributes
   * @returns {Promise<string>}
   */
  async setDisplayUnavailableProductAttributesStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.displayUnavailableAttributesToggleInput(toEnable ? 1 : 0));
    await page.click(this.saveProductsStockFormButton);
    await this.elementNotVisible(page, this.displayUnavailableAttributesToggleInput(!toEnable ? 1 : 0), 2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set separator of attribute anchor on the product links
   * @param page {Page} Browser tab
   * @param separator {string} Value of separator attribute on product page
   * @returns {Promise<string>}
   */
  async setSeparatorOfAttributeOnProductLink(page: Page, separator: string): Promise<string> {
    await this.selectByVisibleText(page, this.separatorAttributeOnProductPageSelect, separator);
    await page.click(this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable allow ordering out of stock
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable allow ordering out of stock status
   * @returns {Promise<string>}
   */
  async setAllowOrderingOutOfStockStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.allowOrderingOosToggleInput(toEnable ? 1 : 0));
    await page.click(this.saveProductsStockFormButton);
    await this.elementNotVisible(page, this.allowOrderingOosToggleInput(!toEnable ? 1 : 0), 2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable stock management
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable stock management status
   * @returns {Promise<string>}
   */
  async setEnableStockManagementStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.enableStockManagementToggleInput(toEnable ? 1 : 0));
    if (toEnable) {
      await this.setChecked(page, this.allowOrderingOosToggleInput(0));
    }
    await page.click(this.saveProductsStockFormButton);
    await this.elementNotVisible(page, this.enableStockManagementToggleInput(!toEnable ? 1 : 0), 2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set number of products displayed per page
   * @param page {Page} Browser tab
   * @param numberOfProducts {number} Value to set on products per page input
   * @return {Promise<string>}
   */
  async setProductsDisplayedPerPage(page: Page, numberOfProducts: number): Promise<string> {
    await this.setValue(page, this.productsPerPageInput, numberOfProducts);
    await page.click(this.savePaginationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change language for selectors
   * @param page {Page} Browser tab
   * @param lang {string} Language to choose
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page: Page, lang: string = 'en'): Promise<void> {
    await Promise.all([
      page.click(this.nameLangButton),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.nameLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Set label of in_stock products
   * @param page {Page} Browser tab
   * @param label {string} Value to set on label of in stock product input
   * @returns {Promise<string>}
   */
  async setLabelOfInStockProducts(page: Page, label: string): Promise<string> {
    // Fill label in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.labelInStock(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.labelInStock(2), label);
    await page.click(this.saveProductsStockFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set delivery time text
   * @param page {Page} Browser tab
   * @param deliveryTimeText {string} Value to set on delivery time in stock input
   * @return {Promise<string>}
   */
  async setDeliveryTimeInStock(page: Page, deliveryTimeText: string): Promise<string> {
    await this.setValue(page, this.deliveryTimeInStockInput, deliveryTimeText);
    await page.click(this.saveProductsStockFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display discounted price
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable display discounted price status
   * @returns {Promise<string>}
   */
  async setDisplayDiscountedPriceStatus(page: Page, toEnable: boolean = true): Promise<string> {
    await this.setChecked(page, this.displayDiscountedPriceToggleInput(toEnable ? 1 : 0));
    await page.click(this.saveProductPageFormButton);
    await this.elementNotVisible(page, this.displayDiscountedPriceToggleInput(!toEnable ? 1 : 0), 2000);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default order for products on products list in FO
   * @param page {Page} Browser tab
   * @param orderBy {string} The order in which products will be displayed in the product list
   * @param orderMethod {string} Order method for product list
   * @return {Promise<string>}
   */
  async setDefaultProductsOrder(page: Page, orderBy: string, orderMethod:string = 'Ascending'): Promise<string> {
    await this.selectByVisibleText(page, this.productsDefaultOrderBySelect, orderBy);
    await this.selectByVisibleText(page, this.productsDefaultOrderMethodSelect, orderMethod);
    await page.click(this.savePaginationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set delivery time out-of-stock text
   * @param page {Page} Browser tab
   * @param deliveryTimeText {string} Vale to set on delivery time out of stock input
   * @return {Promise<string>}
   */
  async setDeliveryTimeOutOfStock(page: Page, deliveryTimeText: string = ''): Promise<string> {
    await this.setValue(page, this.deliveryTimeOutOfStockInput, deliveryTimeText);
    await page.click(this.saveProductsStockFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set label out-of-stock allowed backorders
   * @param page {Page} Browser tab
   * @param label {string} Value to set on label out of stock allowed backorders
   * @returns {Promise<string>}
   */
  async setLabelOosAllowedBackorders(page: Page, label: string): Promise<string> {
    // Fill label in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.oosAllowedBackordersLabel(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.oosAllowedBackordersLabel(2), label);
    await page.click(this.saveProductsStockFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set label out-of-stock denied backorders
   * @param page {Page} Browser tab
   * @param label {string} Value to set on label out of stock denied backorders input
   * @returns {Promise<string>}
   */
  async setLabelOosDeniedBackorders(page: Page, label: string): Promise<string> {
    // Fill label in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.oosDeniedBackordersLabel(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.oosDeniedBackordersLabel(2), label);
    await page.click(this.saveProductsStockFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default pack stock management
   * @param page {Page} Browser tab
   * @param option {string} Option to select on default pack stock management
   * @returns {Promise<string>}
   */
  async setDefaultPackStockManagement(page: Page, option: string): Promise<string> {
    await this.selectByVisibleText(page, this.defaultPackStockManagementSelect, option);
    await page.click(this.saveProductsStockFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new ProductSettings();
