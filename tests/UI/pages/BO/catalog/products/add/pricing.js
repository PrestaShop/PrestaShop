require('module-alias/register');
const AddProductBasePage = require('@pages/BO/catalog/products/add/addProductBasePage');

/**
 * Pricing form, contains functions that can be used on the form
 * @class
 * @extends AddProductBasePage
 */
class Pricing extends AddProductBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on add product page
   */
  constructor() {
    super();

    // Selectors of tab: Pricing
    // Retail price
    this.ecoTaxInput = '#form_step2_ecotax';
    // Specific prices
    this.addSpecificPriceButton = '#js-open-create-specific-price-form';
    this.specificPriceForm = '#specific_price_form';
    this.specificPriceCombinationSelect = '#form_step2_specific_price_sp_id_product_attribute';
    this.specificPriceStartingAtInput = '#form_step2_specific_price_sp_from_quantity';
    this.specificPriceApplyDiscountOfInput = '#form_step2_specific_price_sp_reduction';
    this.specificPriceReductionType = '#form_step2_specific_price_sp_reduction_type';
    this.specificPriceApplyButton = '#form_step2_specific_price_save';
  }

  /*
  Methods
   */
  /**
   * Add specific price
   * @param page {Page} Browser tab
   * @param specificPriceData {Object|{combinations: ?string, discount: ?number, startingAt: ?number,
   * reductionType: ?string}} Data to set on specific price form
   * @return {Promise<string>}
   */
  async addSpecificPrice(page, specificPriceData) {
    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await Promise.all([
      page.click(this.addSpecificPriceButton),
      this.waitForVisibleSelector(page, `${this.specificPriceForm}.show`),
    ]);

    // Choose combinations if exist
    if (specificPriceData.combinations) {
      await this.waitForVisibleSelector(page, this.specificPriceCombinationSelect);
      await this.scrollTo(page, this.specificPriceCombinationSelect);
      await this.selectByVisibleText(page, this.specificPriceCombinationSelect, specificPriceData.combinations);
    }
    await this.setValue(page, this.specificPriceStartingAtInput, specificPriceData.startingAt);
    await this.setValue(page, this.specificPriceApplyDiscountOfInput, specificPriceData.discount);
    await this.selectByVisibleText(page, this.specificPriceReductionType, specificPriceData.reductionType);

    // Apply specific price
    await this.scrollTo(page, this.specificPriceApplyButton);
    await page.click(this.specificPriceApplyButton);

    // Close growl message
    await this.closeGrowlMessage(page);
    await this.goToFormStep(page, 1);
  }

  /**
   * Set ecoTax value and save
   * @param page
   * @param ecoTax
   * @returns {Promise<string>}
   */
  async addEcoTax(page, ecoTax) {
    // Go to pricing tab : id = 2
    await this.goToFormStep(page, 2);
    await Promise.all([
      page.click(this.addSpecificPriceButton),
      this.waitForVisibleSelector(page, `${this.specificPriceForm}.show`),
    ]);

    await this.setValue(page, this.ecoTaxInput, ecoTax);
  }
}

module.exports = new Pricing();
