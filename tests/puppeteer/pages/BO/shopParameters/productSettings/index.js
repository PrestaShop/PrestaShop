require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class productSettings extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Product Settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // Products general form
    this.productGeneralForm = '#configuration_fieldset_products';
    this.switchCatalogModeLabel = 'label[for=\'form_general_catalog_mode_%TOGGLE\']';
    this.switchShowPricesLabel = 'label[for=\'form_general_catalog_mode_with_prices_%TOGGLE\']';
    this.maxSizeShortDescriptionInput = '#form_general_short_description_limit';
    this.newDaysNumberInput = '#form_general_new_days_number';
    this.switchForceUpdateFriendlyURLLabel = 'label[for=\'form_general_force_friendly_url_%TOGGLE\']';
    this.quantityDiscountBasedOnSelect = '#form_general_quantity_discount';
    this.switchDefaultActivationStatusLabel = 'label[for=\'form_general_default_status_%TOGGLE\']';
    this.saveProductGeneralFormButton = `${this.productGeneralForm} .card-footer button`;
    // Product page form
    this.productPageForm = '#configuration_fieldset_fo_product_page';
    this.switchDisplayAvailableQuantities = 'label[for=\'form_page_display_quantities_%TOGGLE\']';
    this.remainingQuantityInput = '#form_page_display_last_quantities';
    this.displayUnavailableAttributesLabel = 'label[for=\'form_page_display_unavailable_attributes_%TOGGLE\']';
    this.separatorAttributeOnProductPageSelect = '#form_page_attribute_anchor_separator';
    this.displayDiscountedPriceLabel = 'label[for=\'form_page_display_discount_price_%TOGGLE\']';
    this.saveProductPageFormButton = `${this.productPageForm} .card-footer button`;
    // Products stock form
    this.productsStockForm = '#configuration_fieldset_stock';
    this.allowOrderingOosLabel = `${this.productsStockForm} label[for='form_stock_allow_ordering_oos_%TOGGLE']`;
    this.enableStockManagementLabel = `${this.productsStockForm} label[for='form_stock_stock_management_%TOGGLE']`;
    this.nameLangButton = '#form_stock_in_stock_label';
    this.nameLangSpan = 'div.dropdown-menu[aria-labelledby=\'form_stock_in_stock_label\'] span[data-locale=\'%LANG\']';
    this.labelInStock = '#form_stock_in_stock_label_%IDLANG';
    this.deliveryTimeInStockInput = '#form_stock_delivery_time_1';
    this.deliveryTimeOutOfStockInput = '#form_stock_oos_delivery_time_1';
    this.oosAllowedBackordersLabel = '#form_stock_oos_allowed_backorders_1';
    this.oosAllowedBackordersLabel = '#form_stock_oos_allowed_backorders_%IDLANG';
    this.oosDeniedBackordersLabel = '#form_stock_oos_denied_backorders_%IDLANG';
    this.saveProductsStockForm = `${this.productsStockForm} .card-footer button`;
    // Pagination form
    this.paginationFormBlock = '#configuration_fieldset_order_by_pagination';
    this.productsPerPageInput = '#form_pagination_products_per_page';
    this.productsDefaultOrderBySelect = '#form_pagination_default_order_by';
    this.productsDefaultOrderMethodSelect = '#form_pagination_default_order_way';
    this.savePaginationFormButton = `${this.paginationFormBlock} .card-footer button`;
  }

  /*
    Methods
  */

  /**
   * Enable/disable catalog mode
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async changeCatalogModeStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchCatalogModeLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/disable show prices
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async setShowPricesStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchShowPricesLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Update number of days
   * @param numberOfDays
   * @returns {Promise<string|*>}
   */
  async updateNumberOfDays(numberOfDays) {
    await this.setValue(this.newDaysNumberInput, numberOfDays.toString());
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Update max size of short description
   * @param size
   * @returns {Promise<string|*>}
   */
  async UpdateMaxSizeOfSummary(size) {
    await this.setValue(this.maxSizeShortDescriptionInput, size.toString());
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable force update of friendly URL
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setForceUpdateFriendlyURLStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchForceUpdateFriendlyURLLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Change default activation status
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDefaultActivationStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchDefaultActivationStatusLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Choose quantity discounts based on
   * @param basedOn
   * @returns {Promise<string>}
   */
  async chooseQuantityDiscountsBasedOn(basedOn) {
    await this.selectByVisibleText(this.quantityDiscountBasedOnSelect, basedOn);
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/ Disable display available quantities
   * @param toEnable
   * @returns {Promise<string|*>}
   */
  async setDisplayAvailableQuantitiesStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchDisplayAvailableQuantities.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductPageFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set display remaining quantities
   * @param quantity
   * @returns {Promise<string>}
   */
  async setDisplayRemainingQuantities(quantity) {
    await this.setValue(this.remainingQuantityInput, quantity.toString());
    await this.clickAndWaitForNavigation(this.saveProductPageFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set display unavailable product attributes
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplayUnavailableProductAttributesStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.displayUnavailableAttributesLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductPageFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set separator of attribute anchor on the product links
   * @param separator
   * @returns {Promise<string>}
   */
  async setSeparatorOfAttributeOnProductLink(separator) {
    await this.selectByVisibleText(this.separatorAttributeOnProductPageSelect, separator);
    await this.clickAndWaitForNavigation(this.saveProductPageFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable allow ordering out of stock
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setAllowOrderingOutOfStockStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.allowOrderingOosLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductsStockForm);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable stock management
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setEnableStockManagementStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.enableStockManagementLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    if (toEnable) {
      await this.waitForSelectorAndClick(this.allowOrderingOosLabel.replace('%TOGGLE', 0));
    }
    await this.clickAndWaitForNavigation(this.saveProductsStockForm);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set number of products displayed per page
   * @param numberOfProducts
   * @return {Promise<string>}
   */
  async setProductsDisplayedPerPage(numberOfProducts) {
    await this.setValue(this.productsPerPageInput, numberOfProducts.toString());
    await this.clickAndWaitForNavigation(this.savePaginationFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Change language for selectors
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(lang = 'en') {
    await Promise.all([
      this.page.click(this.nameLangButton),
      this.waitForVisibleSelector(`${this.nameLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      this.page.click(this.nameLangSpan.replace('%LANG', lang)),
      this.waitForVisibleSelector(`${this.nameLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Set label of in_stock products
   * @param label
   * @returns {Promise<string>}
   */
  async setLabelOfInStockProducts(label) {
    // Fill label in english
    await this.changeLanguageForSelectors('en');
    await this.setValue(this.labelInStock.replace('%IDLANG', 1), label);
    // Fill label in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.labelInStock.replace('%IDLANG', 2), label);
    await this.clickAndWaitForNavigation(this.saveProductsStockForm);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set delivery time text
   * @param deliveryTimeText
   * @return {Promise<string>}
   */
  async setDeliveryTimeInStock(deliveryTimeText) {
    await this.setValue(this.deliveryTimeInStockInput, deliveryTimeText);
    await this.clickAndWaitForNavigation(this.saveProductsStockForm);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set display discounted price
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplayDiscountedPriceStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.displayDiscountedPriceLabel.replace('%TOGGLE', toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductPageFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set default order for products on products list in FO
   * @param orderBy, the order in which products will be displayed in the product list
   * @param orderMethod, order method for product list
   * @return {Promise<string>}
   */
  async setDefaultProductsOrder(orderBy, orderMethod = 'Ascending') {
    await this.selectByVisibleText(this.productsDefaultOrderBySelect, orderBy);
    await this.selectByVisibleText(this.productsDefaultOrderMethodSelect, orderMethod);
    await this.clickAndWaitForNavigation(this.savePaginationFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set delivery time out-of-stock text
   * @param deliveryTimeText
   * @return {Promise<string>}
   */
  async setDeliveryTimeOutOfStock(deliveryTimeText = '') {
    await this.setValue(this.deliveryTimeOutOfStockInput, deliveryTimeText);
    await this.clickAndWaitForNavigation(this.saveProductsStockForm);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set label out-of-stock allowed backorders
   * @param label
   * @returns {Promise<string>}
   */
  async setLabelOosAllowedBackorders(label) {
    // Fill label in english
    await this.changeLanguageForSelectors('en');
    await this.setValue(this.oosAllowedBackordersLabel.replace('%IDLANG', 1), label);
    // Fill label in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.oosAllowedBackordersLabel.replace('%IDLANG', 2), label);
    await this.clickAndWaitForNavigation(this.savePaginationFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Set label out-of-stock denied backorders
   * @param label
   * @returns {Promise<string>}
   */
  async setLabelOosDeniedBackorders(label) {
    // Fill label in english
    await this.changeLanguageForSelectors('en');
    await this.setValue(this.oosDeniedBackordersLabel.replace('%IDLANG', 1), label);
    // Fill label in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.oosDeniedBackordersLabel.replace('%IDLANG', 2), label);
    await this.clickAndWaitForNavigation(this.savePaginationFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
