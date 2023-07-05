import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Delivery slips page, contains functions that can be used on delivery slips page
 * @class
 * @extends BOBasePage
 */
class DeliverySlips extends BOBasePage {
  public readonly pageTitle: string;

  public readonly errorMessageWhenGenerateFileByDate: string;

  private readonly generateByDateForm: string;

  private readonly dateFromInput: string;

  private readonly dateToInput: string;

  private readonly generatePdfByDateButton: string;

  private readonly deliverySlipForm: string;

  private readonly deliveryPrefixInput: string;

  private readonly deliveryNumberInput: string;

  private readonly deliveryProductImageStatusToggleInput: (toggle: number) => string;

  private readonly saveDeliverySlipOptionsButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on delivery slips page
   */
  constructor() {
    super();

    this.pageTitle = `Delivery slips • ${global.INSTALL.SHOP_NAME}`;
    this.errorMessageWhenGenerateFileByDate = 'No delivery slip was found for this period.';
    this.successfulUpdateMessage = 'Update successful';

    // Delivery slips page
    // By date form
    this.generateByDateForm = '#form-delivery-slips-print-pdf';
    this.dateFromInput = '#slip_pdf_form_date_from';
    this.dateToInput = '#slip_pdf_form_date_to';
    this.generatePdfByDateButton = `${this.generateByDateForm} #generate-delivery-slip-by-date`;

    // Delivery slip options form
    this.deliverySlipForm = '#form-delivery-slips-options';
    this.deliveryPrefixInput = '#form_prefix_1';
    this.deliveryNumberInput = '#form_number';
    this.deliveryProductImageStatusToggleInput = (toggle: number) => `#form_enable_product_image_${toggle}`;
    this.saveDeliverySlipOptionsButton = `${this.deliverySlipForm} #save-delivery-slip-options-button`;
  }

  /*
  Methods
   */

  /**
   * Generate PDF by date and download
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on date from input
   * @param dateTo {string} Value to set on date to input
   * @returns {Promise<string>}
   */
  async generatePDFByDateAndDownload(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<string | null> {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);

    return this.clickAndWaitForDownload(page, this.generatePdfByDateButton);
  }

  /**
   * Get message error after generate delivery slip fail
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on date from input
   * @param dateTo {string} Value to set on date to input
   * @returns {Promise<string>}
   */
  async generatePDFByDateAndFail(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<string> {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);
    await page.click(this.generatePdfByDateButton);
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Set values to generate pdf by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on date from input
   * @param dateTo {string} Value to set on date to input
   * @returns {Promise<void>}
   */
  async setValuesForGeneratingPDFByDate(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<void> {
    if (dateFrom) {
      await this.setValue(page, this.dateFromInput, dateFrom);
    }

    if (dateTo) {
      await this.setValue(page, this.dateToInput, dateTo);
    }
  }

  /** Edit delivery slip Prefix
   * @param page {Page} Browser tab
   * @param prefix {string} Prefix value to set
   * @returns {Promise<void>}
   */
  async changePrefix(page: Page, prefix: string): Promise<void> {
    await this.setValue(page, this.deliveryPrefixInput, prefix);
  }

  /** Edit delivery slip number
   * @param page {Page} Browser tab
   * @param number {number} Number value to change
   * @returns {Promise<void>}
   */
  async changeNumber(page: Page, number: number): Promise<void> {
    await this.setValue(page, this.deliveryNumberInput, number);
  }

  /**
   * Enable disable product image
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable product image
   * @returns {Promise<void>}
   */
  async setEnableProductImage(page: Page, enable: boolean = true): Promise<void> {
    await this.setChecked(page, this.deliveryProductImageStatusToggleInput(enable ? 1 : 0));
  }

  /** Save delivery slip options
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveDeliverySlipOptions(page: Page): Promise<string> {
    await page.click(this.saveDeliverySlipOptionsButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new DeliverySlips();
