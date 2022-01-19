require('module-alias/register');
const AddProductBasePage = require('@pages/BO/catalog/products/add/addProductBasePage');
const basicSettings = require('@pages/BO/catalog/products/add/basicSettings');
const quantities = require('@pages/BO/catalog/products/add/quantities');
const pricing = require('@pages/BO/catalog/products/add/pricing');
const combinations = require('@pages/BO/catalog/products/add/combinations');

/**
 * Add product page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddProduct extends AddProductBasePage {
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

    // Header selectors
    this.productNameInput = '#form_step1_name_1';
    this.productTypeSelect = '#form_step1_type_product';

    // Selectors of form nav list
    this.formNavList = '#form-nav';
    this.forNavListItemLink = id => `${this.formNavList} #tab_step${id} a`;

    // Footer selectors
    this.productDeleteLink = '.product-footer a.delete';
    this.productOnlineTitle = 'h2.for-switch.online-title';
    this.productOnlineSwitch = '.product-footer div.switch-input';
    this.previewProductLink = 'a#product_form_preview_btn';
    this.saveProductButton = 'input#submit[value=\'Save\']';
    this.goToCatalogButton = '#product_form_save_go_to_catalog_btn';
    this.addNewProductButton = '#product_form_save_new_btn';
  }

  /*
  Methods
   */

  /**
   * Create/Edit product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on on add/edit product form
   * @returns {Promise<string>}
   */
  async setProduct(page, productData) {
    // Set product name
    await this.setValue(page, this.productNameInput, productData.name);
    // Select type
    await this.selectByVisibleText(page, this.productTypeSelect, productData.type, true);
    // Set basic settings form
    await basicSettings.setBasicSettings(page, productData);
    if (productData.type === 'Pack of products') {
      await basicSettings.addPackOfProducts(page, productData.pack);
    }
    // Set combinations form
    if (productData.productHasCombinations) {
      await combinations.setCombinationsInProduct(page, productData);
      await this.reloadPage(page);
    }
    // Set quantities/virtual form
    if (!productData.productHasCombinations) {
      await quantities.setQuantitiesSettings(page, productData);
    }
    // Set pricing form
    if (productData.productWithSpecificPrice) {
      await pricing.addSpecificPrice(page, productData.specificPrice);
    }
    if (productData.productWithEcoTax) {
      await pricing.addEcoTax(page, productData.ecoTax);
    }
    // Set status
    await this.setProductStatus(page, productData.status);

    // Save product
    return this.saveProduct(page);
  }
}

module.exports = new AddProduct();
