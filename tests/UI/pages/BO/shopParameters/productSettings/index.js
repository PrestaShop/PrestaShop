require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class ProductSettings extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Product Settings •';
    this.successfulUpdateMessage = 'Update successful';

    // Selectors
    // Products general form
    this.productGeneralForm = '#configuration_fieldset_products';
    this.switchCatalogModeLabel = toggle => `label[for='form_general_catalog_mode_${toggle}']`;
    this.switchShowPricesLabel = toggle => `label[for='form_general_catalog_mode_with_prices_${toggle}']`;
    this.maxSizeShortDescriptionInput = '#form_general_short_description_limit';
    this.newDaysNumberInput = '#form_general_new_days_number';
    this.switchForceUpdateFriendlyURLLabel = toggle => `label[for='form_general_force_friendly_url_${toggle}']`;
    this.quantityDiscountBasedOnSelect = '#form_general_quantity_discount';
    this.switchDefaultActivationStatusLabel = toggle => `label[for='form_general_default_status_${toggle}']`;
    this.saveProductGeneralFormButton = `${this.productGeneralForm} .card-footer button`;
    // Product page form
    this.productPageForm = '#configuration_fieldset_fo_product_page';
    this.switchDisplayAvailableQuantities = toggle => `label[for='form_page_display_quantities_${toggle}']`;
    this.remainingQuantityInput = '#form_page_display_last_quantities';
    this.displayUnavailableAttributesLabel = toggle => 'label'
      + `[for='form_page_display_unavailable_attributes_${toggle}']`;
    this.separatorAttributeOnProductPageSelect = '#form_page_attribute_anchor_separator';
    this.displayDiscountedPriceLabel = toggle => `label[for='form_page_display_discount_price_${toggle}']`;
    this.saveProductPageFormButton = `${this.productPageForm} .card-footer button`;
    // Products stock form
    this.productsStockForm = '#configuration_fieldset_stock';
    this.allowOrderingOosLabel = toggle => `${this.productsStockForm} label`
      + `[for='form_stock_allow_ordering_oos_${toggle}']`;
    this.enableStockManagementLabel = toggle => `${this.productsStockForm} label`
      + `[for='form_stock_stock_management_${toggle}']`;
    this.nameLangButton = '#form_stock_in_stock_label';
    this.nameLangSpan = lang => 'div.dropdown-menu[aria-labelledby=\'form_stock_in_stock_label\']'
      + ` span[data-locale='${lang}']`;
    this.labelInStock = idLang => `#form_stock_in_stock_label_${idLang}`;
    this.deliveryTimeInStockInput = '#form_stock_delivery_time_1';
    this.deliveryTimeOutOfStockInput = '#form_stock_oos_delivery_time_1';
    this.oosAllowedBackordersLabel = '#form_stock_oos_allowed_backorders_1';
    this.oosAllowedBackordersLabel = idLang => `#form_stock_oos_allowed_backorders_${idLang}`;
    this.oosDeniedBackordersLabel = idLang => `#form_stock_oos_denied_backorders_${idLang}`;
    this.defaultPackStockManagementSelect = '#form_stock_pack_stock_management';
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
   * @param page
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async changeCatalogModeStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchCatalogModeLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/disable show prices
   * @param page
   * @param toEnable, true to enable and false to disable
   * @return {Promise<string>}
   */
  async setShowPricesStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchShowPricesLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Update number of days
   * @param page
   * @param numberOfDays
   * @returns {Promise<string>}
   */
  async updateNumberOfDays(page, numberOfDays) {
    await this.setValue(page, this.newDaysNumberInput, numberOfDays.toString());
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Update max size of short description
   * @param page
   * @param size
   * @returns {Promise<string>}
   */
  async UpdateMaxSizeOfSummary(page, size) {
    await this.setValue(page, this.maxSizeShortDescriptionInput, size.toString());
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable force update of friendly URL
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setForceUpdateFriendlyURLStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchForceUpdateFriendlyURLLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change default activation status
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDefaultActivationStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchDefaultActivationStatusLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Choose quantity discounts based on
   * @param page
   * @param basedOn
   * @returns {Promise<string>}
   */
  async chooseQuantityDiscountsBasedOn(page, basedOn) {
    await this.selectByVisibleText(page, this.quantityDiscountBasedOnSelect, basedOn);
    await this.clickAndWaitForNavigation(page, this.saveProductGeneralFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/ Disable display available quantities
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplayAvailableQuantitiesStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchDisplayAvailableQuantities(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display remaining quantities
   * @param page
   * @param quantity
   * @returns {Promise<string>}
   */
  async setDisplayRemainingQuantities(page, quantity) {
    await this.setValue(page, this.remainingQuantityInput, quantity.toString());
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display unavailable product attributes
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplayUnavailableProductAttributesStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.displayUnavailableAttributesLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set separator of attribute anchor on the product links
   * @param page
   * @param separator
   * @returns {Promise<string>}
   */
  async setSeparatorOfAttributeOnProductLink(page, separator) {
    await this.selectByVisibleText(page, this.separatorAttributeOnProductPageSelect, separator);
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable allow ordering out of stock
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setAllowOrderingOutOfStockStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.allowOrderingOosLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable/Disable stock management
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setEnableStockManagementStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.enableStockManagementLabel(toEnable ? 1 : 0));
    if (toEnable) {
      await this.waitForSelectorAndClick(page, this.allowOrderingOosLabel(0));
    }
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set number of products displayed per page
   * @param page
   * @param numberOfProducts
   * @return {Promise<string>}
   */
  async setProductsDisplayedPerPage(page, numberOfProducts) {
    await this.setValue(page, this.productsPerPageInput, numberOfProducts.toString());
    await this.clickAndWaitForNavigation(page, this.savePaginationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Change language for selectors
   * @param page
   * @param lang
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
   * @param page
   * @param label
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
   * @param page
   * @param deliveryTimeText
   * @return {Promise<string>}
   */
  async setDeliveryTimeInStock(page, deliveryTimeText) {
    await this.setValue(page, this.deliveryTimeInStockInput, deliveryTimeText);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set display discounted price
   * @param page
   * @param toEnable
   * @returns {Promise<string>}
   */
  async setDisplayDiscountedPriceStatus(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.displayDiscountedPriceLabel(toEnable ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveProductPageFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default order for products on products list in FO
   * @param page
   * @param orderBy, the order in which products will be displayed in the product list
   * @param orderMethod, order method for product list
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
   * @param page
   * @param deliveryTimeText
   * @return {Promise<string>}
   */
  async setDeliveryTimeOutOfStock(page, deliveryTimeText = '') {
    await this.setValue(page, this.deliveryTimeOutOfStockInput, deliveryTimeText);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set label out-of-stock allowed backorders
   * @param page
   * @param label
   * @returns {Promise<string>}
   */
  async setLabelOosAllowedBackorders(page, label) {
    // Fill label in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.oosAllowedBackordersLabel(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.oosAllowedBackordersLabel(2), label);
    await this.clickAndWaitForNavigation(page, this.savePaginationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set label out-of-stock denied backorders
   * @param page
   * @param label
   * @returns {Promise<string>}
   */
  async setLabelOosDeniedBackorders(page, label) {
    // Fill label in english
    await this.changeLanguageForSelectors(page, 'en');
    await this.setValue(page, this.oosDeniedBackordersLabel(1), label);
    // Fill label in french
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.oosDeniedBackordersLabel(2), label);
    await this.clickAndWaitForNavigation(page, this.savePaginationFormButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set default pack stock management
   * @param page
   * @param option
   * @returns {Promise<string>}
   */
  async setDefaultPackStockManagement(page, option) {
    await this.selectByVisibleText(page, this.defaultPackStockManagementSelect, option);
    await this.clickAndWaitForNavigation(page, this.saveProductsStockForm);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new ProductSettings();
