import BOBasePage from '@pages/BO/BObasePage';

import type ProductData from '@data/faker/product';
import type {
  ProductAttributes, ProductCustomization, ProductPackItem, ProductSpecificPrice,
} from '@data/types/product';

import type {Page} from 'playwright';

/**
 * Add product page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddProduct extends BOBasePage {
  public readonly pageTitle: string;

  public readonly settingUpdatedMessage: string;

  public readonly duplicateSuccessfulMessage: string;

  public readonly errorMessage: string;

  public readonly errorMessageWhenSummaryTooLong: (number: number) => string;

  private readonly productNameInput: string;

  private readonly productImageDropZoneDiv: string;

  private readonly productTypeSelect: string;

  private readonly openFileManagerDiv: string;

  private readonly imagePreviewBlock: string;

  private readonly productWithCombinationsInput: string;

  private readonly productReferenceInput: string;

  private readonly productQuantityInput: string;

  private readonly productPriceAtiInput: string;

  private readonly saveProductButton: string;

  private readonly goToCatalogButton: string;

  private readonly addNewProductButton: string;

  private readonly previewProductLink: string;

  private readonly productOnlineSwitch: string;

  private readonly productOnlineTitle: string;

  private readonly productShortDescriptionIframe: string;

  private readonly productDescriptionIframe: string;

  private readonly productTaxRuleSelect: string;

  private readonly productDeleteLink: string;

  private readonly dangerMessageShortDescription: string;

  private readonly packItemsInput: string;

  private readonly packsearchResult: string;

  private readonly packQuantityInput: string;

  private readonly addProductToPackButton: string;

  private readonly formNavList: string;

  private readonly forNavListItemLink: (id: number) => string;

  private readonly ecoTaxInput: string;

  private readonly addSpecificPriceButton: string;

  private readonly specificPriceForm: string;

  private readonly combinationSelect: string;

  private readonly startingAtInput: string;

  private readonly applyDiscountOfInput: string;

  private readonly reductionType: string;

  private readonly applyButton: string;

  private readonly deleteSpecificPriceButton: (row: number) => string;

  private readonly onSaleCheckbox: string;

  private readonly selectAttributeInput: string;

  private readonly generateCombinationsButton: string;

  private readonly productCombinationBulkQuantityInput: string;

  private readonly productCombinationSelectAllCheckbox: string;

  private readonly applyOnCombinationsButton: string;

  private readonly productCombinationTableRow: (id: number) => string;

  private readonly deleteCombinationsButton: string;

  private readonly productCombinationsBulkForm: string;

  private readonly productCombinationsBulkFormTitle: string;

  private readonly bulkCombinationsContainer: string;

  private readonly quantityInput: string;

  private readonly minimumQuantityInput: string;

  private readonly stockLocationInput: string;

  private readonly lowStockLevelInput: string;

  private readonly behaviourOutOfStockInput: (id: number) => string;

  private readonly labelWhenInStockInput: string;

  private readonly labelWhenOutOfStock: string;

  private readonly resetUrlButton: string;

  private readonly friendlyUrlInput: string;

  private readonly customFieldsBlock: string;

  private readonly addCustomizationFieldButton: string;

  private readonly customFieldInput: (row: number) => string;

  private readonly customFieldTypeSelect: (row: number) => string;

  private readonly customRequiredLabel: (row: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add product page
   */
  constructor() {
    super();

    this.pageTitle = 'Product •';

    // Text Message
    this.settingUpdatedMessage = 'Settings updated.';
    this.duplicateSuccessfulMessage = 'Product successfully duplicated.';
    this.errorMessage = 'Unable to update settings.';
    this.errorMessageWhenSummaryTooLong = (number: number) => 'This value is too long.'
      + ` It should have ${number} characters or less.`;

    // Selectors
    this.productNameInput = '#form_step1_name_1';
    this.productImageDropZoneDiv = '#product-images-dropzone';
    this.productTypeSelect = '#form_step1_type_product';
    this.openFileManagerDiv = `${this.productImageDropZoneDiv} .disabled.openfilemanager.dz-clickable`;
    this.imagePreviewBlock = `${this.productImageDropZoneDiv} > div.dz-complete.dz-image-preview`;
    this.productWithCombinationsInput = '#show_variations_selector div:nth-of-type(2) input';
    this.productReferenceInput = '#form_step6_reference';
    this.productQuantityInput = '#form_step1_qty_0_shortcut';
    this.productPriceAtiInput = '#form_step1_price_ttc_shortcut';
    this.saveProductButton = 'input#submit[value=\'Save\']';
    this.goToCatalogButton = '#product_form_save_go_to_catalog_btn';
    this.addNewProductButton = '#product_form_save_new_btn';
    this.previewProductLink = 'a#product_form_preview_btn';
    this.productOnlineSwitch = '.product-footer div.switch-input';
    this.productOnlineTitle = 'h2.for-switch.online-title';
    this.productShortDescriptionIframe = '#form_step1_description_short div.translation-field[data-locale="en"]';
    this.productDescriptionIframe = '#form_step1_description div.translation-field[data-locale="en"]';
    this.productTaxRuleSelect = '#step2_id_tax_rules_group_rendered';
    this.productDeleteLink = '.product-footer a.delete';
    this.dangerMessageShortDescription = '#form_step1_description_short .has-danger li';
    this.packItemsInput = '#form_step1_inputPackItems';
    this.packsearchResult = '#js_form_step1_inputPackItems .tt-selectable tr:nth-child(1) td:nth-child(1)';
    this.packQuantityInput = '#form_step1_inputPackItems-curPackItemQty';
    this.addProductToPackButton = '#form_step1_inputPackItems-curPackItemAdd';

    // Form nav
    this.formNavList = '#form-nav';
    this.forNavListItemLink = (id: number) => `${this.formNavList} #tab_step${id} a`;

    // Selectors of Step 2 : Pricing
    this.ecoTaxInput = '#form_step2_ecotax';
    this.addSpecificPriceButton = '#js-open-create-specific-price-form';
    this.specificPriceForm = '#specific_price_form';
    this.combinationSelect = '#form_step2_specific_price_sp_id_product_attribute';
    this.startingAtInput = '#form_step2_specific_price_sp_from_quantity';
    this.applyDiscountOfInput = '#form_step2_specific_price_sp_reduction';
    this.reductionType = '#form_step2_specific_price_sp_reduction_type';
    this.applyButton = '#form_step2_specific_price_save';
    this.deleteSpecificPriceButton = (row: number) => `#js-specific-price-list tr:nth-child(${row}) td a.delete`;
    this.onSaleCheckbox = '#form_step2_on_sale';

    // Selector of Step 3 : Combinations
    this.selectAttributeInput = '#form_step3_attributes-tokenfield';
    this.generateCombinationsButton = '#create-combinations';
    this.productCombinationBulkQuantityInput = '#product_combination_bulk_quantity';
    this.productCombinationSelectAllCheckbox = 'input#toggle-all-combinations';
    this.applyOnCombinationsButton = '#apply-on-combinations';
    this.productCombinationTableRow = (id: number) => `#accordion_combinations tr:nth-of-type(${id})`;
    this.deleteCombinationsButton = '#delete-combinations';
    this.productCombinationsBulkForm = '#combinations-bulk-form';
    this.productCombinationsBulkFormTitle = `${this.productCombinationsBulkForm} p[aria-controls]`;
    this.bulkCombinationsContainer = '#bulk-combinations-container';

    // Selector of step 3 : Quantities
    this.quantityInput = '#form_step3_qty_0';
    this.minimumQuantityInput = '#form_step3_minimal_quantity';
    this.stockLocationInput = '#form_step3_location';
    this.lowStockLevelInput = '#form_step3_low_stock_threshold';
    this.behaviourOutOfStockInput = (id: number) => `#form_step3_out_of_stock_${id}`;
    this.labelWhenInStockInput = '#form_step3_available_now_1';
    this.labelWhenOutOfStock = '#form_step3_available_later_1';

    // Selector of Step 5 : SEO
    this.resetUrlButton = '#seo-url-regenerate';
    this.friendlyUrlInput = '#form_step5_link_rewrite_1';

    // Selectors of step 6 : Options
    this.customFieldsBlock = '#custom_fields';
    this.addCustomizationFieldButton = `${this.customFieldsBlock} a[data-role='add-customization-field']`;
    this.customFieldInput = (row: number) => `#form_step6_custom_fields_${row}_label_1`;
    this.customFieldTypeSelect = (row: number) => `select#form_step6_custom_fields_${row}_type`;
    this.customRequiredLabel = (row: number) => `${this.customFieldsBlock} li:nth-child(${row}) div.required-custom-field `
      + 'label';
  }

  /*
  Methods
   */

  /**
   * Set value on tinyMce textarea
   * @param page {Page} Browser tab
   * @param selector {string} Value of selector to use
   * @param value {string} Text to set on tinymce input
   * @returns {Promise<void>}
   */
  async setValueOnTinymceInput(page: Page, selector: string, value: string): Promise<void> {
    // Select all
    await page.click(`${selector} .mce-edit-area`, {clickCount: 3});

    // Delete all text
    await page.keyboard.press('Backspace');

    // Fill the text
    await page.keyboard.type(value);
  }

  /**
   * Get Number of images to set on the product
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfImages(page: Page): Promise<number> {
    return (await page.$$(this.imagePreviewBlock)).length;
  }

  /**
   * Add product images
   * @param page {Page} Browser tab
   * @param imagesPaths {Array<?string>} Paths of the images to add to the product
   * @returns {Promise<void>}
   */
  async addProductImages(page: Page, imagesPaths: (string | null)[] = []): Promise<void> {
    const filteredImagePaths: string[] = imagesPaths.filter((el: string | null): el is string => el !== null);

    if (filteredImagePaths !== null && filteredImagePaths.length !== 0) {
      const numberOfImages = await this.getNumberOfImages(page);
      await this.uploadOnFileChooser(
        page,
        numberOfImages === 0 ? this.productImageDropZoneDiv : this.openFileManagerDiv,
        filteredImagePaths,
      );

      await this.waitForVisibleSelector(page, this.imagePreviewBlock);
    }
  }

  /**
   * Set product quantity
   * @param page {Page} Browser tab
   * @param quantity {number} The product quantity to set in the input
   */
  async setProductQuantity(page: Page, quantity: number): Promise<void> {
    await this.goToFormStep(page, 1);
    await this.setValue(page, this.productQuantityInput, quantity);
  }

  /**
   * Set Name, type of product, Reference, price ATI, description and short description
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on basic settings form
   * @return {Promise<void>}
   */
  async setBasicSetting(page: Page, productData: ProductData): Promise<void> {
    await this.setValue(page, this.productNameInput, productData.name);

    // Set product images
    await this.addProductImages(page, [productData.coverImage, productData.thumbImage]);

    await this.setValueOnTinymceInput(page, this.productDescriptionIframe, productData.description);
    await this.setValueOnTinymceInput(page, this.productShortDescriptionIframe, productData.summary);
    await this.selectByVisibleText(page, this.productTypeSelect, productData.type, true);
    await this.setValue(page, this.productReferenceInput, productData.reference);
    if (await this.elementVisible(page, this.productQuantityInput, 500)) {
      await this.setValue(page, this.productQuantityInput, productData.quantity);
    }
    await this.selectByVisibleText(page, this.productTaxRuleSelect, productData.taxRule);
    await this.setValue(page, this.productPriceAtiInput, productData.price);
  }

  /**
   * Set product online or offline
   * @param page {Page} Browser tab
   * @param wantedStatus {boolean} True if we need to enable status, false if not
   * @return {Promise<void>}
   */
  async setProductStatus(page: Page, wantedStatus: boolean): Promise<void> {
    const isProductOnline = await this.getOnlineButtonStatus(page);

    if (isProductOnline !== wantedStatus) {
      await page.click(this.productOnlineSwitch);
      await this.closeGrowlMessage(page);
    }
  }

  /**
   * Save product and close the growl message linked to
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async saveProduct(page: Page): Promise<string | null> {
    await page.click(this.saveProductButton);
    const growlTextMessage = await this.getGrowlMessageContent(page, 30000);
    await this.closeGrowlMessage(page);

    return growlTextMessage;
  }

  /**
   * Create basic product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on new/edit product form
   * @returns {Promise<string>}
   */
  async createEditBasicProduct(page: Page, productData: ProductData): Promise<string | null> {
    await this.setBasicSetting(page, productData);

    if (productData.type === 'Pack of products') {
      await this.addPackOfProducts(page, productData.pack);
    }

    await this.setProductStatus(page, productData.status);
    return this.saveProduct(page);
  }

  /**
   * Set attributes for product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on combination form
   * @returns {Promise<string>}
   */
  async setAttributesInProduct(page: Page, productData: ProductData): Promise<string | null> {
    await page.click(this.productWithCombinationsInput);
    // GOTO Combination tab : id = 3
    await this.goToFormStep(page, 3);
    // Delete All combinations if exists
    await this.deleteAllAttributes(page);
    // Add combinations
    await this.selectAttributes(page, productData.attributes);
    // Set quantity
    await this.setAttributesQuantity(page, productData.quantity);
    // GOTO Basic settings Tab : id = 1
    await this.goToFormStep(page, 1);

    return this.saveProduct(page);
  }

  /**
   * Generate combinations in input
   * @param page {Page} Browser tab
   * @param attributes {ProductAttributes[]} Data to set on combination form
   * @return {Promise<void>}
   */
  async selectAttributes(page: Page, attributes: ProductAttributes[]) {
    if (attributes.length > 0) {
      for (let i = 0; i < attributes.length; i++) {
        for (let j = 0; j < attributes[i].values.length; j++) {
          await this.addAttribute(page, `${attributes[i].name} : ${attributes[i].values[j]}`);
        }
      }
    }
    /* eslint-enable */
    await page.$eval(this.generateCombinationsButton, (el: HTMLElement) => el.click());
    await this.closeGrowlMessage(page);
  }

  /**
   * Add one attribute
   * @param page {Page} Browser tab
   * @param attribute {string} Data to set on combination form
   * @return {Promise<void>}
   */
  async addAttribute(page: Page, attribute: string): Promise<void> {
    await page.locator(this.selectAttributeInput).fill(attribute);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
  }

  /**
   * Set quantity for all attributes
   * @param page {Page} Browser tab
   * @param quantity {number} Value of quantity to set on quantity input
   * @return {Promise<void>}
   */
  async setAttributesQuantity(page: Page, quantity: number): Promise<void> {
    // Select all combinations
    await this.waitForVisibleSelector(page, this.productCombinationSelectAllCheckbox);
    await page.$eval(this.productCombinationSelectAllCheckbox, (el: HTMLElement) => el.click());

    // Open combinations bulk form
    if (await this.elementNotVisible(page, this.productCombinationBulkQuantityInput, 1000)) {
      await page.click(this.productCombinationsBulkFormTitle);
      await this.waitForVisibleSelector(page, this.productCombinationBulkQuantityInput, 5000);
    }

    // Edit quantity
    await page.locator(this.productCombinationBulkQuantityInput).fill(quantity.toString());
    await this.scrollTo(page, this.applyOnCombinationsButton);
    await page.click(this.applyOnCombinationsButton);

    // Close growl message
    await this.closeGrowlMessage(page);
  }

  /**
   * Preview product in new tab
   * @param page {Page} Browser tab
   * @return page opened
   */
  async previewProduct(page: Page): Promise<Page> {
    await this.waitForVisibleSelector(page, this.previewProductLink);
    const newPage = await this.openLinkWithTargetBlank(page, this.previewProductLink, 'body a');
    const textBody = await this.getTextContent(newPage, 'body');

    if (textBody.includes('[Debug] This page has moved')) {
      await this.clickAndWaitForURL(newPage, 'a');
    }
    return newPage;
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async deleteProduct(page: Page): Promise<string> {
    await Promise.all([
      this.waitForVisibleSelector(page, this.modalDialog),
      page.click(this.productDeleteLink),
    ]);
    await this.clickAndWaitForURL(page, this.modalDialogYesButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Navigate between forms in add product
   * @param page {Page} Browser tab
   * @param id {number} Value of form id to go
   * @return {Promise<void>}
   */
  async goToFormStep(page: Page, id: number = 1): Promise<void> {
    const selector = this.forNavListItemLink(id);
    await Promise.all([
      this.waitForVisibleSelector(page, `${selector}[aria-selected='true']`),
      this.waitForSelectorAndClick(page, selector),
    ]);
  }

  /**
   * Return true if combinations table is displayed
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  hasCombinations(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productCombinationTableRow(1), 2000);
  }

  /**
   * Delete all attributes
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async deleteAllAttributes(page: Page): Promise<void> {
    if (await this.hasCombinations(page)) {
      // Select all combinations
      await this.setChecked(page, this.productCombinationSelectAllCheckbox);

      // Open combinations bulk form
      if (await this.elementNotVisible(page, this.productCombinationBulkQuantityInput, 1000)) {
        await page.click(this.productCombinationsBulkFormTitle);
        await this.waitForVisibleSelector(page, this.productCombinationBulkQuantityInput, 5000);
      }

      // Scroll and click on delete combinations button
      await this.scrollTo(page, this.deleteCombinationsButton);

      await Promise.all([
        page.click(this.deleteCombinationsButton),
        this.waitForVisibleSelector(page, this.modalDialog),
      ]);
      await page.waitForTimeout(250);
      await page.click(this.modalDialogYesButton);
      await this.closeGrowlMessage(page);
    }
  }

  /**
   * Reset friendly URL
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async resetURL(page: Page): Promise<void> {
    await this.goToFormStep(page, 5);
    await this.waitForVisibleSelector(page, this.resetUrlButton);
    await this.scrollTo(page, this.resetUrlButton);
    await page.click(this.resetUrlButton);
    await this.goToFormStep(page, 1);
  }

  /**
   * Get the error message when short description is too long
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getErrorMessageWhenSummaryIsTooLong(page: Page): Promise<string> {
    return this.getTextContent(page, this.dangerMessageShortDescription);
  }

  /**
   * Get friendly URL
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getFriendlyURL(page: Page): Promise<string | null> {
    await this.reloadPage(page);
    await this.goToFormStep(page, 5);

    return this.getAttributeContent(page, this.friendlyUrlInput, 'value');
  }

  /**
   * Add customized value
   * @param page {Page} Browser tab
   * @param customizationData {ProductCustomization} Data to set on customized form
   * @param row {number} Row of input
   * @returns {Promise<string>}
   */
  async addCustomization(page: Page, customizationData: ProductCustomization, row: number = 0): Promise<string | null> {
    // Go to options tab : id = 6
    await this.goToFormStep(page, 6);
    await Promise.all([
      page.click(this.addCustomizationFieldButton),
      this.waitForVisibleSelector(page, this.customFieldInput(row)),
    ]);

    await this.setValue(page, this.customFieldInput(row), customizationData.label);
    await this.selectByVisibleText(page, this.customFieldTypeSelect(row), customizationData.type);

    if (customizationData.required) {
      await this.waitForSelectorAndClick(page, this.customRequiredLabel(row + 1));
    }

    return this.saveProduct(page);
  }

  /**
   * Add specific prices
   * @param page {Page} Browser tab
   * @param specificPriceData {ProductSpecificPrice} Data to set on specific price form
   * @return {Promise<string|null>}
   */
  async addSpecificPrices(page: Page, specificPriceData: ProductSpecificPrice): Promise<string | null> {
    await this.reloadPage(page);

    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await Promise.all([
      page.click(this.addSpecificPriceButton),
      this.waitForVisibleSelector(page, `${this.specificPriceForm}.show`),
    ]);

    // Choose combinations if exist
    if (specificPriceData.attributes) {
      await this.waitForVisibleSelector(page, this.combinationSelect);
      await this.scrollTo(page, this.combinationSelect);
      await this.selectByVisibleText(page, this.combinationSelect, specificPriceData.attributes);
    }
    await this.setValue(page, this.startingAtInput, specificPriceData.startingAt);
    await this.setValue(page, this.applyDiscountOfInput, specificPriceData.discount);
    await this.selectByVisibleText(page, this.reductionType, specificPriceData.reductionType);

    // Apply specific price
    await this.scrollTo(page, this.applyButton);
    await page.click(this.applyButton);

    // Get growl message
    const growlMessageText = await this.getGrowlMessageContent(page, 30000);

    await this.closeGrowlMessage(page);
    await this.goToFormStep(page, 1);

    return growlMessageText;
  }

  /**
   * Delete specific price
   * @param page {Page} Browser tab
   * @param row {number} Row in specific price table
   * @return {promise<string | null>}
   */
  async deleteSpecificPrice(page: Page, row: number = 1): Promise<string | null> {
    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await this.waitForSelectorAndClick(page, this.deleteSpecificPriceButton(row));
    await Promise.all([
      this.waitForVisibleSelector(page, this.modalDialog),
      page.click(this.modalDialogYesButton),
    ]);

    return this.getGrowlMessageContent(page, 30000);
  }

  /**
   * Display on sale flag
   * @param page {Page} Browser tab
   * @param onSale {boolean} True if we need to display on sale flag
   * @returns {Promise<string>}
   */
  async displayOnSaleFlag(page: Page, onSale: boolean = true): Promise<string | null> {
    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await this.setChecked(page, this.onSaleCheckbox, onSale);

    return this.saveProduct(page);
  }

  /**
   * Get online product status
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  getOnlineButtonStatus(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productOnlineTitle, 1000);
  }

  /**
   * Is quantity input visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isQuantityInputVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productQuantityInput, 1000);
  }

  /**
   * Go to catalog page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCatalogPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.goToCatalogButton);
  }

  /**
   * Go to add product page
   * @param page
   * @returns {Promise<void>}
   */
  async goToAddProductPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewProductButton);
  }

  /**
   * Add product to pack
   * @param page {Page} Browser tab
   * @param product {string} Value of product name to set on input
   * @param quantity {number} Value of quantity to set on input
   * @returns {Promise<void>}
   */
  async addProductToPack(page: Page, product: string, quantity: number): Promise<void> {
    await page.locator(this.packItemsInput).fill(product);
    await this.waitForSelectorAndClick(page, this.packsearchResult);
    await this.setValue(page, this.packQuantityInput, quantity);
    await page.click(this.addProductToPackButton);
  }

  /**
   * Add pack of products
   * @param page {Page} Browser tab
   * @param pack {ProductPackItem[]} Data to set on pack form
   * @returns {Promise<void>}
   */
  async addPackOfProducts(page: Page, pack: ProductPackItem[]): Promise<void> {
    for (let i: number = 0; i < pack.length; i++) {
      await this.addProductToPack(page, pack[i].reference, pack[i].quantity);
    }
  }

  /**
   * Get product name from input
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getProductName(page: Page): Promise<string | null> {
    return this.getAttributeContent(page, this.productNameInput, 'value');
  }

  /**
   * Set quantities settings
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on quantities setting form
   * @returns {Promise<void>}
   */
  async setQuantitiesSettings(page: Page, productData: ProductData): Promise<void> {
    let columnSelector;
    // Go to Quantities tab
    await this.goToFormStep(page, 3);
    // Set Quantities form
    await this.setValue(page, this.quantityInput, productData.quantity);
    await this.setValue(page, this.minimumQuantityInput, productData.minimumQuantity);
    // Set Stock form
    await this.setValue(page, this.stockLocationInput, productData.stockLocation);
    await this.setValue(page, this.lowStockLevelInput, productData.lowStockLevel);

    // Set Availability preferences form
    switch (productData.behaviourOutOfStock) {
      case 'Deny orders':
        columnSelector = this.behaviourOutOfStockInput(0);
        break;

      case 'Allow orders':
        columnSelector = this.behaviourOutOfStockInput(1);
        break;

      case 'Default behavior':
        columnSelector = this.behaviourOutOfStockInput(2);
        break;

      default:
        throw new Error(`Column ${productData.behaviourOutOfStock} was not found`);
    }

    await page.$eval(columnSelector, (el: HTMLElement) => el.click());

    // Set value on label In and out of stock inputs
    await this.scrollTo(page, this.labelWhenInStockInput);
    await this.setValue(page, this.labelWhenInStockInput, productData.labelWhenInStock);
    await this.setValue(page, this.labelWhenOutOfStock, productData.labelWhenOutOfStock);
  }

  /**
   * Set product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on add/edit product form
   * @returns {Promise<string|null>}
   */
  async setProduct(page: Page, productData: ProductData): Promise<string | null> {
    await this.setBasicSetting(page, productData);
    if (productData.type === 'Pack of products') {
      await this.addPackOfProducts(page, productData.pack);
    }
    if (productData.productHasCombinations) {
      await this.setAttributesInProduct(page, productData);
    }
    await this.setProductStatus(page, productData.status);
    if (!productData.productHasCombinations) {
      await this.setQuantitiesSettings(page, productData);
    }
    return this.saveProduct(page);
  }

  /**
   * Set ecoTax value and save
   * @param page
   * @param ecoTax
   * @returns {Promise<string|null>}
   */
  async addEcoTax(page: Page, ecoTax: number): Promise<string | null> {
    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await Promise.all([
      page.click(this.addSpecificPriceButton),
      this.waitForVisibleSelector(page, `${this.specificPriceForm}.show`),
    ]);

    await this.setValue(page, this.ecoTaxInput, ecoTax.toString());
    return this.saveProduct(page);
  }
}

export default new AddProduct();
