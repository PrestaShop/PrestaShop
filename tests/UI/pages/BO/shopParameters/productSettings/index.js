require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Product settings page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class ProductSettings extends BOBasePage {
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
    this.catalogModeToggleInput = toggle => `#general_catalog_mode_${toggle}`;
    this.showPricesToggleInput = toggle => `#general_catalog_mode_with_prices_${toggle}`;
    this.maxSizeShortDescriptionInput = '#general_short_description_limit';
    this.newDaysNumberInput = '#general_new_days_number';
    this.forceUpdateFriendlyUrlToggleInput = toggle => `#general_force_friendly_url_${toggle}`;
    this.quantityDiscountBasedOnSelect = '#general_quantity_discount';
    this.defaultActivationStatusToggleInput = toggle => `#general_default_status_${toggle}`;
    this.saveProductGeneralFormButton = '#form-general-save-button';

    // Product page form
    this.displayAvailableQuantitiesToggleInput = toggle => `#page_display_quantities_${toggle}`;
    this.remainingQuantityInput = '#page_display_last_quantities';
    this.displayUnavailableAttributesToggleInput = toggle => `#page_display_unavailable_attributes_${toggle}`;
    this.separatorAttributeOnProductPageSelect = '#page_attribute_anchor_separator';
    this.displayDiscountedPriceToggleInput = toggle => `#page_display_discount_price_${toggle}`;
    this.saveProductPageFormButton = '#form-page-save-button';

    // Products stock form
    this.productsStockForm = '#configuration_fieldset_stock';
    this.allowOrderingOosToggleInput = toggle => `#stock_allow_ordering_oos_${toggle}`;
    this.enableStockManagementToggleInput = toggle => `#stock_stock_management_${toggle}`;
    this.nameLangButton = '#stock_in_stock_label';
    this.nameLangSpan = lang => 'div.dropdown-menu[aria-labelledby=\'stock_in_stock_label\']'
      + ` span[data-locale='${lang}']`;
    this.labelInStock = idLang => `#stock_in_stock_label_${idLang}`;
    this.deliveryTimeInStockInput = '#stock_delivery_time_1';
    this.deliveryTimeOutOfStockInput = '#stock_oos_delivery_time_1';
    this.oosAllowedBackordersLabel = idLang => `#stock_oos_allowed_backorders_${idLang}`;
    this.oosDeniedBackordersLabel = idLang => `#stock_oos_denied_backorders_${idLang}`;
    this.defaultPackStockManagementSelect = '#stock_pack_stock_management';
    this.saveProductsStockForm = '#form-stock-save-button';
    this.saveProductsStockForm = `${this.productsStockForm} .card-footer button`;

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
  async changeCatalogModeStatus(page, toEnable = true) {
    await page.check(this.catalogModeToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/disable show prices
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable show prices status
   * @return {Promise<string>}
   */
  async setShowPricesStatus(page, toEnable = true) {
    await page.check(this.showPricesToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Update number of days
   * @param page {Page} Browser tab
   * @param numberOfDays {number} Value to set on number of days input
   * @returns {Promise<string>}
   */
  async updateNumberOfDays(page, numberOfDays) {
    await this.setValue(page, this.newDaysNumberInput, numberOfDays.toString());
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Update max size of short description
   * @param page {Page} Browser tab
   * @param size {number} Value to set on size input
   * @returns {Promise<string>}
   */
  async UpdateMaxSizeOfSummary(page, size) {
    await this.setValue(page, this.maxSizeShortDescriptionInput, size.toString());
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable force update of friendly URL
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable force update friendly url status
   * @returns {Promise<string>}
   */
  async setForceUpdateFriendlyURLStatus(page, toEnable = true) {
    await page.check(this.forceUpdateFriendlyUrlToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change default activation status
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable default activation status
   * @returns {Promise<string>}
   */
  async setDefaultActivationStatus(page, toEnable = true) {
    await page.check(this.defaultActivationStatusToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Choose quantity discounts based on
   * @param page {Page} Browser tab
   * @param basedOn {string} Value of quantity discount based on
   * @returns {Promise<string>}
   */
  async chooseQuantityDiscountsBasedOn(page, basedOn) {
    await this.selectByVisibleText(page, this.quantityDiscountBasedOnSelect, basedOn);
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/ Disable display available quantities
   * @param page {Page} Browser tab
   * @param toEnable {boolean} Status to set to display available quantity
   * @returns {Promise<string>}
   */
  async setDisplayAvailableQuantitiesStatus(page, toEnable = true) {
    await page.check(this.displayAvailableQuantitiesToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display remaining quantities
   * @param page {Page} Browser tab
   * @param quantity {number} Value of remaining quantity to set
   * @returns {Promise<string>}
   */
  async setDisplayRemainingQuantities(page, quantity) {
    await this.setValue(page, this.remainingQuantityInput, quantity.toString());
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display unavailable product attributes
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable unavailable attributes
   * @returns {Promise<string>}
   */
  async setDisplayUnavailableProductAttributesStatus(page, toEnable = true) {
    await page.check(this.displayUnavailableAttributesToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set separator of attribute anchor on the product links
   * @param page {Page} Browser tab
   * @param separator {string} Value of separator attribute on product page
   * @returns {Promise<string>}
   */
  async setSeparatorOfAttributeOnProductLink(page, separator) {
    await this.selectByVisibleText(page, this.separatorAttributeOnProductPageSelect, separator);
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable allow ordering out of stock
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable allow ordering out of stock status
   * @returns {Promise<string>}
   */
  async setAllowOrderingOutOfStockStatus(page, toEnable = true) {
    await page.check(this.allowOrderingOosToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable stock management
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable stock management status
   * @returns {Promise<string>}
   */
  async setEnableStockManagementStatus(page, toEnable = true) {
    await page.check(this.enableStockManagementToggleInput(toEnable ? 1 : 0));
    if (toEnable) {
      await page.check(this.allowOrderingOosToggleInput(0));
    }
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set number of products displayed per page
   * @param page {Page} Browser tab
   * @param numberOfProducts {number} Value to set on products per page input
   * @return {Promise<string>}
   */
  async setProductsDisplayedPerPage(page, numberOfProducts) {
    await this.setValue(page, this.productsPerPageInput, numberOfProducts.toString());
    await this.clickAndWaitForNavigation(page, this.savePaginationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change language for selectors
   * @param page {Page} Browser tab
   * @param lang {string} Language to choose
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page, lang = 'en') {
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
  async setLabelOfInStockProducts(page, label) {
    // Fill label in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.labelInStock(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.labelInStock(2), label);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set delivery time text
   * @param page {Page} Browser tab
   * @param deliveryTimeText {string} Value to set on delivery time in stock input
   * @return {Promise<string>}
   */
  async setDeliveryTimeInStock(page, deliveryTimeText) {
    await this.setValue(page, this.deliveryTimeInStockInput, deliveryTimeText);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display discounted price
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable display discounted price status
   * @returns {Promise<string>}
   */
  async setDisplayDiscountedPriceStatus(page, toEnable = true) {
    await page.check(this.displayDiscountedPriceToggleInput(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default order for products on products list in FO
   * @param page {Page} Browser tab
   * @param orderBy {string} The order in which products will be displayed in the product list
   * @param orderMethod {string} Order method for product list
   * @return {Promise<string>}
   */
  async setDefaultProductsOrder(page, orderBy, orderMethod = 'Ascending') {
    await this.selectByVisibleText(page, this.productsDefaultOrderBySelect, orderBy);
    await this.selectByVisibleText(page, this.productsDefaultOrderMethodSelect, orderMethod);
    await this.clickAndWaitForNavigation(page, this.savePaginationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set delivery time out-of-stock text
   * @param page {Page} Browser tab
   * @param deliveryTimeText {string} Vale to set on delivery time out of stock input
   * @return {Promise<string>}
   */
  async setDeliveryTimeOutOfStock(page, deliveryTimeText = '') {
    await this.setValue(page, this.deliveryTimeOutOfStockInput, deliveryTimeText);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set label out-of-stock allowed backorders
   * @param page {Page} Browser tab
   * @param label {string} Value to set on label out of stock allowed backorders
   * @returns {Promise<string>}
   */
  async setLabelOosAllowedBackorders(page, label) {
    // Fill label in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.oosAllowedBackordersLabel(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.oosAllowedBackordersLabel(2), label);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set label out-of-stock denied backorders
   * @param page {Page} Browser tab
   * @param label {string} Value to set on label out of stock denied backorders input
   * @returns {Promise<string>}
   */
  async setLabelOosDeniedBackorders(page, label) {
    // Fill label in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.oosDeniedBackordersLabel(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.oosDeniedBackordersLabel(2), label);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default pack stock management
   * @param page {Page} Browser tab
   * @param option {string} Option to select on default pack stock management
   * @returns {Promise<string>}
   */
  async setDefaultPackStockManagement(page, option) {
    await this.selectByVisibleText(page, this.defaultPackStockManagementSelect, option);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new ProductSettings();
