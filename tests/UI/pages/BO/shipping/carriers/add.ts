// Import BO pages
import BOBasePage from '@pages/BO/BObasePage';

import {
  CarrierRange,
  FakerCarrier,
} from '@prestashop-core/ui-testing';

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

  private readonly rangeInfInput: (numColumn: number) => string;

  private readonly rangeSupInput: (numColumn: number) => string;

  private readonly allZonesRadioButton: string;

  private readonly allZonesValueInput: (numColumn: number) => string;

  private readonly rangeZoneCheckbox: (zoneID: string) => string;

  private readonly rangePriceInput: (numColumn: number, zoneID: string) => string;

  private readonly addNewRangeButton: string;

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
    this.rangeInfInput = (numColumn: number) => `${this.zonesTable} tr.range_inf td:nth-child(${numColumn + 3}) `
      + 'input[name^=\'range_inf\']';
    this.rangeSupInput = (numColumn: number) => `${this.zonesTable} tr.range_sup td:nth-child(${numColumn + 3}) `
      + 'input[name^=\'range_sup\']';
    this.allZonesRadioButton = `${this.zonesTable} tr.fees_all input[onclick*='checkAllZones']`;
    this.allZonesValueInput = (numColumn: number) => `${this.zonesTable} tr.fees_all td:nth-child(${numColumn + 3})`
      + ' .input-group input';
    this.rangeZoneCheckbox = (zoneID: string) => `${this.zonesTable} #zone_${zoneID}`;
    this.rangePriceInput = (numColumn: number, zoneID: string) => `${this.zonesTable} td:nth-child(${numColumn + 3})`
      + ` input[name^="fees[${zoneID}]"]`;
    this.addNewRangeButton = '#add_new_range';

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
   * @param carrierData {FakerCarrier} Carrier information
   * @return {Promise<string>}
   */
  async createEditCarrier(page: Page, carrierData: FakerCarrier): Promise<string> {
    // Set general settings
    await this.setValue(page, this.nameInput, carrierData.name);
    await this.setValue(page, this.transitTimeInput, carrierData.transitName);
    await this.setValue(page, this.speedGradeInput, carrierData.speedGrade);
    await this.uploadFile(page, this.logoInput, `${carrierData.name}.jpg`);
    await this.setValue(page, this.trackingURLInput, carrierData.trackingURL);
    await page.locator(this.nextButton).click();

    // Set shipping locations and costs
    await this.setChecked(page, this.addHandlingCostsToggle(carrierData.handlingCosts ? 'on' : 'off'));
    await this.setChecked(page, this.freeShippingToggle(carrierData.freeShipping ? 'on' : 'off'));

    if (carrierData.billing === 'According to total price') {
      await page.locator(this.billingPriceRadioButton).click();
    } else {
      await page.locator(this.billingWeightButton).click();
    }
    await this.selectByVisibleText(page, this.taxRuleSelect, carrierData.taxRule);
    await this.selectByVisibleText(page, this.rangeBehaviorSelect, carrierData.outOfRangeBehavior);

    // Set range sup only if free shipping is disabled
    if (!carrierData.freeShipping) {
      for (let idxRange: number = 0; idxRange < carrierData.ranges.length; idxRange++) {
        const carrierRange: CarrierRange = carrierData.ranges[idxRange];

        if (carrierRange.weightMin) {
          await this.setValue(page, this.rangeInfInput(idxRange), carrierRange.weightMin);
        }
        if (carrierRange.weightMax) {
          await this.setValue(page, this.rangeSupInput(idxRange), carrierRange.weightMax);
        }

        for (let idxZone: number = 0; idxZone < carrierRange.zones.length; idxZone++) {
          const carrierRangeZone = carrierRange.zones[idxZone].zone;
          const carrierRangePrice = carrierRange.zones[idxZone].price;

          if (typeof carrierRangeZone === 'string') {
            await page.locator(this.allZonesRadioButton).setChecked(true);
            if (carrierRangePrice) {
              await page.locator(this.allZonesValueInput(idxRange)).fill(carrierRangePrice.toString());
              await page.waitForTimeout(1000);
              await page.locator(this.allZonesValueInput(idxRange)).dispatchEvent('change');
              await page.waitForTimeout(2000);
            }
          } else {
            await page.locator(
              this.rangeZoneCheckbox(carrierRangeZone.id!.toString()),
            ).setChecked(true);
            if (carrierRangePrice) {
              await page.locator(
                this.rangePriceInput(idxRange, carrierRangeZone.id!.toString()),
              ).fill(carrierRangePrice.toString());
            }
          }
        }

        // Click on the "Add new range" button
        if (idxRange < (carrierData.ranges.length - 1)) {
          await page.locator(this.addNewRangeButton).click();
        }
      }
    }
    await page.locator(this.nextButton).click();

    // Set size, weight and group access
    await this.setValue(page, this.maxWidthInput, carrierData.maxWidth);
    await this.setValue(page, this.maxHeightInput, carrierData.maxHeight);
    await this.setValue(page, this.maxDepthInput, carrierData.maxDepth);
    await this.setValue(page, this.maxWeightInput, carrierData.maxWeight);
    await page.locator(this.nextButton).click();

    // Summary
    await this.setChecked(page, this.enableToggle(carrierData.enable ? 'on' : 'off'));
    await page.locator(this.finishButton).click();

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
    await page.locator(this.nextButton).click();
    await this.setChecked(page, this.addHandlingCostsToggle(toEnable ? 'on' : 'off'));

    await page.locator(this.finishButton).click();

    // Return successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddCarrier();
