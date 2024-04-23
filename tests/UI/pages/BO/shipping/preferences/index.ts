import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import {
  // Import data
  FakerCarrier,
} from '@prestashop-core/ui-testing';

class Preferences extends BOBasePage {
  public readonly pageTitle: string;

  private readonly handlingForm: string;

  private readonly handlingChargesInput: string;

  private readonly saveHandlingButton: string;

  private readonly carrierOptionForm: string;

  private readonly defaultCarrierSelect: string;

  private readonly sortBySelect: string;

  private readonly orderBySelect: string;

  private readonly saveCarrierOptionsButton: string;

  constructor() {
    super();

    this.pageTitle = 'Preferences •';
    this.successfulUpdateMessage = 'Update successful';

    // Handling form selectors
    this.handlingForm = '#handling';
    this.handlingChargesInput = '#handling_shipping_handling_charges';
    this.saveHandlingButton = `${this.handlingForm} button`;

    // Carrier options selectors
    this.carrierOptionForm = '#carrier-options';
    this.defaultCarrierSelect = '#carrier-options_default_carrier';
    this.sortBySelect = '#carrier-options_carrier_default_order_by';
    this.orderBySelect = '#carrier-options_carrier_default_order_way';
    this.saveCarrierOptionsButton = `${this.carrierOptionForm} button`;
  }

  /* Handling methods */

  /**
   * Set handling charges button
   * @param page {Page} Browser tab
   * @param value {string} The handling charges value
   * @returns {Promise<string>}
   */
  async setHandlingCharges(page: Page, value: string): Promise<string> {
    await this.setValue(page, this.handlingChargesInput, value);

    // Save handling form and return successful message
    await page.locator(this.saveHandlingButton).click();
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /* Carrier options methods */

  /**
   * Set default carrier in carrier options form
   * @param page {Page} Browser tab
   * @param carrier {FakerCarrier} List of carriers
   * @return {Promise<string>}
   */
  async setDefaultCarrier(page: Page, carrier: FakerCarrier): Promise<string> {
    await this.selectByVisibleText(
      page,
      this.defaultCarrierSelect,
      `${carrier.id} - ${carrier.name} (${carrier.delay})`,
    );

    // Save configuration and return successful message
    await page.locator(this.saveCarrierOptionsButton).click();
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set carriers sort By 'Price' or 'Position' / order by 'Ascending' or 'descending' in carrier options form
   * @param page {Page} Browser tab
   * @param sortBy {String} Sort by 'Price' or 'Position'
   * @param orderBy {String} Order by 'Ascending' or 'Descending'
   * @returns {Promise<string>}
   */
  async setCarrierSortOrderBy(page: Page, sortBy: string, orderBy: string = 'Ascending'): Promise<string> {
    await this.selectByVisibleText(page, this.sortBySelect, sortBy);
    await this.selectByVisibleText(page, this.orderBySelect, orderBy);

    // Save configuration and return successful message
    await page.locator(this.saveCarrierOptionsButton).click();
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new Preferences();
