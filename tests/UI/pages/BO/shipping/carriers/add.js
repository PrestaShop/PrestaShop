require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddCarrier extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Carriers > View •';
    this.pageTitleEdit = 'Carriers >';

    this.alertSuccessBlockParagraph = '.alert-success';

    // General settings
    this.carrierForm = '#carrier_wizard';
    this.nameInput = '#name';
    this.transitTimeInput = '#delay_1';
    this.speedGradeInput = '#grade';
    this.logoInput = '#attachement_filename';
    this.trackingURLInput = '#url';

    // Shipping locations and costs
    this.freeShippingToggle = toggle => `${this.carrierForm} label[for='is_free_${toggle}']`;
    this.billingPriceRadioButton = '#billing_price';
    this.billingWeightButton = '#billing_weight';
    this.taxRuleSelect = '#id_tax_rules_group';
    this.rangeBehaviorSelect = '#range_behavior';

    // Size, weight and group access
    this.maxWidthInput = '#max_width';
    this.maxHeightInput = '#max_height';
    this.maxDepthInput = '#max_depth';
    this.maxWeightInput = '#max_weight';

    // Summary
    this.enableToggle = toggle => `${this.carrierForm} label[for='active_${toggle}']`;

    this.nextButton = `${this.carrierForm} .buttonNext`;
    this.finishButton = `${this.carrierForm} .buttonFinish`;
  }

  /* Methods */

  /**
   * Fill carrier form in create or edit page and save
   * @param page
   * @param carrierData
   * @return {Promise<string>}
   */
  async createEditCarrier(page, carrierData) {
    // Set general settings
    await this.setValue(page, this.nameInput, carrierData.name);
    await this.setValue(page, this.transitTimeInput, carrierData.transitName);
    await this.setValue(page, this.speedGradeInput, carrierData.speedGrade);
    await this.setValue(page, this.trackingURLInput, carrierData.trakingURL);
    await page.click(this.nextButton);

    // Set shipping locations and costs
    await page.click(this.freeShippingToggle(carrierData.freeShipping ? 'on' : 'off'));
    await this.selectByVisibleText(this.taxRuleSelect, carrierData.taxRule);
    await this.selectByVisibleText(this.rangeBehaviorSelect, carrierData.outOfRangeBehavior);
    await page.click(this.nextButton);

    // Set size, weight and group access
    await this.setValue(page, this.maxWidthInput, carrierData.maxWidth);
    await this.setValue(page, this.maxHeightInput, carrierData.maxHeight);
    await this.setValue(page, this.maxDepthInput, carrierData.maxDepth);
    await this.setValue(page, this.maxWeightInput, carrierData.maxWeight);
    await page.click(this.nextButton);

    // Summary
    await page.click(this.enableToggle(carrierData.enable ? 'on' : 'off'));
    await page.click(this.finishButton);

    // Return successful message
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }
}

module.exports = new AddCarrier();
