require('module-alias/register');
const AddProductBasePage = require('@pages/BO/catalog/products/add/addProductBasePage');

/**
 * Combinations form, contains functions that can be used on the form
 * @class
 * @extends AddProductBasePage
 */
class Combinations extends AddProductBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add product page
   */
  constructor() {
    super();

    // Selectors of tab: Combinations
    this.combinationsInput = '#form_step3_attributes-tokenfield';
    this.generateCombinationsButton = '#create-combinations';
    this.productCombinationSelectAllCheckbox = 'input#toggle-all-combinations';
    this.productCombinationTableRow = row => `#accordion_combinations tr:nth-of-type(${row})`;
    // Bulk actions form
    this.productCombinationsBulkForm = '#combinations-bulk-form';
    this.productCombinationsBulkFormTitle = `${this.productCombinationsBulkForm} p[aria-controls]`;
    this.productCombinationBulkQuantityInput = '#product_combination_bulk_quantity';
    this.applyOnCombinationsButton = '#apply-on-combinations';
    this.deleteCombinationsButton = '#delete-combinations';
  }

  /*
  Methods
   */
  /**
   * Return true if combinations table is displayed
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  isCombinationsTableVisible(page) {
    return this.elementVisible(page, this.productCombinationTableRow(1), 2000);
  }


  /**
   * Add one combination
   * @param page {Page} Browser tab
   * @param combination {string} Data to set on combination form
   * @return {Promise<void>}
   */
  async addCombination(page, combination) {
    await page.type(this.combinationsInput, combination);
    await page.keyboard.press('ArrowDown');
    await page.keyboard.press('Enter');
  }

  /**
   * Generate combinations in input
   * @param page {Page} Browser tab
   * @param combinations {Object|{color: Array<string>, size: Array<string>}} Data to set on combination form
   * @return {Promise<void>}
   */
  async addCombinations(page, combinations) {
    const keys = Object.keys(combinations);
    /*eslint-disable*/
    for (const key of keys) {
      for (const value of combinations[key]) {
        await this.addCombination(page, `${key} : ${value}`);
      }
    }
    /* eslint-enable */
    await page.$eval(this.generateCombinationsButton, el => el.click());
    await this.closeGrowlMessage(page);
  }

  /**
   * Set quantity for all combinations
   * @param page {Page} Browser tab
   * @param quantity {number} Value of quantity to set on quantity input
   * @return {Promise<void>}
   */
  async setCombinationsQuantity(page, quantity) {
    // Select all combinations
    await this.setChecked(page, this.productCombinationSelectAllCheckbox);

    // Open combinations bulk form
    if (await this.elementNotVisible(page, this.productCombinationBulkQuantityInput, 1000)) {
      await page.click(this.productCombinationsBulkFormTitle);
      await this.waitForVisibleSelector(page, this.productCombinationBulkQuantityInput, 5000);
    }

    // Edit quantity
    await page.type(this.productCombinationBulkQuantityInput, quantity.toString());
    await this.scrollTo(page, this.applyOnCombinationsButton);
    await page.click(this.applyOnCombinationsButton);

    // Close growl message
    await this.closeGrowlMessage(page);
  }

  /**
   * Delete all combinations
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async deleteAllCombinations(page) {
    if (await this.isCombinationsTableVisible(page)) {
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
   * Set Combinations for product
   * @param page {Page} Browser tab
   * @param productData {ProductData} Data to set on combination form
   * @returns {Promise<string>}
   */
  async setCombinationsInProduct(page, productData) {
    // Go to Combination tab : id = 3
    await this.goToFormStep(page, 3);
    // Delete All combinations if exists
    await this.deleteAllCombinations(page);
    // Add combinations
    await this.addCombinations(page, productData.combinations);
    // Set quantity
    await this.setCombinationsQuantity(page, productData.quantity);
  }
}

module.exports = new Combinations();
