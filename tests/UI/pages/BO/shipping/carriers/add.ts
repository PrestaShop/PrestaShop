// Import BO pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import CarrierData from '@data/faker/carrier';

import type {Page} from 'playwright';

/**
 * Add carrier page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddCarrier extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly carrierForm: string;

  private readonly nameInput: string;

  private readonly transitTimeInput: string;

  private readonly speedGradeInput: string;

  private readonly logoInput: string;

  private readonly trackingURLInput: string;

  private readonly addHandlingCostsToggle: (toggle: string) => string;

  private readonly freeShippingToggle: (toggle: string) => string;

  private readonly billingPriceRadioButton: string;

  private readonly billingWeightButton: string;

  private readonly taxRuleSelect: string;

  private readonly rangeBehaviorSelect: string;

  private readonly zonesTable: string;

  private readonly rangeSupInput: string;

  private readonly allZonesRadioButton: string;

  private readonly allZonesValueInput: string;

  private readonly zoneRadioButton: (zoneID: string) => string;

  private readonly maxWidthInput: string;

  private readonly maxHeightInput: string;

  private readonly maxDepthInput: string;

  private readonly maxWeightInput: string;

  private readonly enableToggle: (toggle: string) => string;

  private readonly nextButton: string;

  private readonly finishButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on add carrier page
   */
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
    this.logoInput = '#carrier_logo_input';
    this.trackingURLInput = '#url';

    // Shipping locations and costs
    this.addHandlingCostsToggle = (toggle: string) => `${this.carrierForm} #shipping_handling_${toggle}`;
    this.freeShippingToggle = (toggle: string) => `${this.carrierForm} #is_free_${toggle}`;
    this.billingPriceRadioButton = '#billing_price';
    this.billingWeightButton = '#billing_weight';
    this.taxRuleSelect = '#id_tax_rules_group';
    this.rangeBehaviorSelect = '#range_behavior';
    this.zonesTable = '#zones_table';
    this.rangeSupInput = `${this.zonesTable} tr.range_sup td.range_data input[name*='range_sup']`;
    this.allZonesRadioButton = `${this.zonesTable} tr.fees_all input[onclick*='checkAllZones']`;
    this.allZonesValueInput = `${this.zonesTable} tr.fees_all .input-group input`;
    this.zoneRadioButton = (zoneID: string) => `${this.zonesTable} #zone_${zoneID}`;

    // Size, weight and group access
    this.maxWidthInput = '#max_width';
    this.maxHeightInput = '#max_height';
    this.maxDepthInput = '#max_depth';
    this.maxWeightInput = '#max_weight';

    // Summary
    this.enableToggle = (toggle: string) => `${this.carrierForm} #active_${toggle}`;

    this.nextButton = `${this.carrierForm} .buttonNext`;
    this.finishButton = `${this.carrierForm} .buttonFinish`;
  }

  /* Methods */

  /**
   * Fill carrier form in create or edit page and save
   * @param page {Page} Browser tab
   * @param carrierData {CarrierData} Carrier information
   * @return {Promise<string>}
   */
  async createEditCarrier(page: Page, carrierData: CarrierData): Promise<string> {
    // Set general settings
    await this.setValue(page, this.nameInput, carrierData.name);
    await this.setValue(page, this.transitTimeInput, carrierData.transitName);
    await this.setValue(page, this.speedGradeInput, carrierData.speedGrade);
    await this.uploadFile(page, this.logoInput, `${carrierData.name}.jpg`);
    await this.setValue(page, this.trackingURLInput, carrierData.trakingURL);
    await page.click(this.nextButton);

    // Set shipping locations and costs
    await this.setChecked(page, this.addHandlingCostsToggle(carrierData.handlingCosts ? 'on' : 'off'));
    await this.setChecked(page, this.freeShippingToggle(carrierData.freeShipping ? 'on' : 'off'));

    if (carrierData.billing === 'According to total price') {
      await page.click(this.billingPriceRadioButton);
    } else {
      await page.click(this.billingWeightButton);
    }
    await this.selectByVisibleText(page, this.taxRuleSelect, carrierData.taxRule);
    await this.selectByVisibleText(page, this.rangeBehaviorSelect, carrierData.outOfRangeBehavior);

    // Set range sup only if free shipping is disabled
    if (!carrierData.freeShipping) {
      await this.setValue(page, this.rangeSupInput, carrierData.rangeSup);

      if (carrierData.allZones) {
        await page.click(this.allZonesRadioButton);
        await this.setValue(page, this.allZonesValueInput, carrierData.allZonesValue);
      } else {
        await page.click(this.zoneRadioButton(carrierData.zoneID.toString()));
      }
    }
    await page.click(this.nextButton);

    // Set size, weight and group access
    await this.setValue(page, this.maxWidthInput, carrierData.maxWidth);
    await this.setValue(page, this.maxHeightInput, carrierData.maxHeight);
    await this.setValue(page, this.maxDepthInput, carrierData.maxDepth);
    await this.setValue(page, this.maxWeightInput, carrierData.maxWeight);
    await page.click(this.nextButton);

    // Summary
    await this.setChecked(page, this.enableToggle(carrierData.enable ? 'on' : 'off'));
    await page.click(this.finishButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set handling cost
   * @param page {Page} Browser tab
   * @param toEnable {Boolean} Handling cost toggle button value
   * @returns {Promise<string>}
   */
  async setHandlingCosts(page: Page, toEnable: boolean = true): Promise<string> {
    await page.click(this.nextButton);
    await this.setChecked(page, this.addHandlingCostsToggle(toEnable ? 'on' : 'off'));

    await page.click(this.finishButton);

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddCarrier();
