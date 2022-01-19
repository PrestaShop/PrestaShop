require('module-alias/register');
const AddProductBasePage = require('@pages/BO/catalog/products/add/addProductBasePage');

/**
 * Quantities form, contains functions that can be used on the form
 * @class
 * @extends AddProductBasePage
 */
class Quantities extends AddProductBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add product page
   */
  constructor() {
    super();

    // Selectors of tab: Quantities
    // Quantities
    this.quantityInput = '#form_step3_qty_0';
    this.minimumQuantityInput = '#form_step3_minimal_quantity';
    // Stocks
    this.stockLocationInput = '#form_step3_location';
    this.lowStockLevelInput = '#form_step3_low_stock_threshold';
    // Availability preferences
    this.behaviourOutOfStockInput = id => `#form_step3_out_of_stock_${id}`;
    this.labelWhenInStockInput = '#form_step3_available_now_1';
    this.labelWhenOutOfStock = '#form_step3_available_later_1';
  }

  /*
  Methods
   */
  /**
   * Set quantities settings
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on quantities setting form
   * @returns {Promise<void>}
   */
  async setQuantitiesSettings(page, productData) {
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

    await page.$eval(columnSelector, el => el.click());

    // Set value on label In and out of stock inputs
    await this.scrollTo(page, this.labelWhenInStockInput);
    await this.setValue(page, this.labelWhenInStockInput, productData.labelWhenInStock);
    await this.setValue(page, this.labelWhenOutOfStock, productData.LabelWhenOutOfStock);
  }
}

module.exports = new Quantities();
