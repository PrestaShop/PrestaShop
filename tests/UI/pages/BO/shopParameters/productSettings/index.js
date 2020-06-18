require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class productSettings extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Product Settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // Products general form
    this.switchCatalogModeLabel = toggle => `label[for='general_catalog_mode_${toggle}']`;
    this.switchShowPricesLabel = toggle => `label[for='general_catalog_mode_with_prices_${toggle}']`;
    this.maxSizeShortDescriptionInput = '#general_short_description_limit';
    this.newDaysNumberInput = '#general_new_days_number';
    this.switchForceUpdateFriendlyURLLabel = toggle => `label[for='general_force_friendly_url_${toggle}']`;
    this.quantityDiscountBasedOnSelect = '#general_quantity_discount';
    this.switchDefaultActivationStatusLabel = toggle => `label[for='general_default_status_${toggle}']`;
    this.saveProductGeneralFormButton = '#form-general-save-button';

    // Product page form
    this.switchDisplayAvailableQuantities = toggle => `label[for='page_display_quantities_${toggle}']`;
    this.remainingQuantityInput = '#page_display_last_quantities';
    this.displayUnavailableAttributesLabel = toggle => `label[for='page_display_unavailable_attributes_${toggle}']`;
    this.separatorAttributeOnProductPageSelect = '#page_attribute_anchor_separator';
    this.displayDiscountedPriceLabel = toggle => `label[for='page_display_discount_price_${toggle}']`;
    this.saveProductPageFormButton = '#form-page-save-button';

    // Products stock form
    this.productsStockForm = '#configuration_fieldset_stock';
    this.allowOrderingOosLabel = toggle => `${this.productsStockForm} label`
      + `[for='stock_allow_ordering_oos_${toggle}']`;
    this.enableStockManagementLabel = toggle => `${this.productsStockForm} label`
      + `[for='stock_stock_management_${toggle}']`;
    this.nameLangButton = '#stock_in_stock_label';
    this.nameLangSpan = lang => 'div.dropdown-menu[aria-labelledby=\'stock_in_stock_label\']'
      + ` span[data-locale='${lang}']`;
    this.labelInStock = idLang => `#stock_in_stock_label_${idLang}`;
    this.deliveryTimeInStockInput = '#stock_delivery_time_1';
    this.deliveryTimeOutOfStockInput = '#stock_oos_delivery_time_1';
    this.oosAllowedBackordersLabel = idLang => `#stock_oos_allowed_backorders_${idLang}`;
    this.oosDeniedBackordersLabel = idLang => `#stock_oos_denied_backorders_${idLang}`;
    this.saveProductsStockForm = '#form-stock-save-button';

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
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async changeCatalogModeStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchCatalogModeLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/disable show prices
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async setShowPricesStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchShowPricesLabel(toEnable ? 1 : 0));
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
    await this.waitForSelectorAndClick(this.switchForceUpdateFriendlyURLLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductGeneralFormButton);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Change default activation status
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDefaultActivationStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.switchDefaultActivationStatusLabel(toEnable ? 1 : 0));
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
    await this.waitForSelectorAndClick(this.switchDisplayAvailableQuantities(toEnable ? 1 : 0));
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
    await this.waitForSelectorAndClick(this.displayUnavailableAttributesLabel(toEnable ? 1 : 0));
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
    await this.waitForSelectorAndClick(this.allowOrderingOosLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(this.saveProductsStockForm);
    return this.getTextContent(this.alertSuccessBlock);
  }

  /**
   * Enable/Disable stock management
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setEnableStockManagementStatus(toEnable = true) {
    await this.waitForSelectorAndClick(this.enableStockManagementLabel(toEnable ? 1 : 0));
    if (toEnable) {
      await this.waitForSelectorAndClick(this.allowOrderingOosLabel(0));
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
      this.page.click(this.nameLangSpan(lang)),
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
    await this.setValue(this.labelInStock(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.labelInStock(2), label);
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
    await this.waitForSelectorAndClick(this.displayDiscountedPriceLabel(toEnable ? 1 : 0));
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
    await this.setValue(this.oosAllowedBackordersLabel(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.oosAllowedBackordersLabel(2), label);
    await this.clickAndWaitForNavigation(this.saveProductsStockForm);
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
    await this.setValue(this.oosDeniedBackordersLabel(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors('fr');
    await this.setValue(this.oosDeniedBackordersLabel(2), label);
    await this.clickAndWaitForNavigation(this.saveProductsStockForm);
    return this.getTextContent(this.alertSuccessBlock);
  }
};
