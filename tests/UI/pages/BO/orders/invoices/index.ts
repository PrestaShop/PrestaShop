import BOBasePage from '@pages/BO/BObasePage';

import type InvoiceData from '@data/faker/invoice';

import type {Page} from 'playwright';

/**
 * Invoices page, contains functions that can be used on invoices page
 * @class
 * @extends BOBasePage
 */
class Invoice extends BOBasePage {
  public readonly pageTitle: string;

  public readonly errorMessageWhenGenerateFileByDate: string;

  public readonly errorMessageWhenGenerateFileByStatus: string;

  public readonly errorMessageWhenNotSelectStatus: string;

  private readonly generateByDateForm: string;

  private readonly dateFromInput: string;

  private readonly dateToInput: string;

  private readonly generatePdfByDateButton: string;

  private readonly generateByStatusForm: string;

  private readonly statusOrderStateSpan: string;

  private readonly generatePdfByStatusButton: string;

  private readonly invoiceOptionsForm: string;

  private readonly invoiceOptionsStatusToggleInput: (toggle: number) => string;

  private readonly taxBreakdownStatusToggleInput: (toggle: number) => string;

  private readonly invoiceOptionStatusToggleInput: (toggle: number) => string;

  private readonly invoiceNumberInput: string;

  private readonly legalFreeTextInput: string;

  private readonly footerTextInput: string;

  private readonly saveInvoiceOptionsButton: string;

  private readonly invoicePrefixInput: string;

  private readonly invoiceAddCurrentYearToggleInput: (toggle: number) => string;

  private readonly optionYearPositionRadioButton: (id: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on invoices page
   */
  constructor() {
    super();

    this.pageTitle = 'Invoices';
    this.errorMessageWhenGenerateFileByDate = 'No invoice has been found for this period.';
    this.errorMessageWhenGenerateFileByStatus = 'No invoice has been found for this status.';
    this.errorMessageWhenNotSelectStatus = 'You must select at least one order status.';
    this.successfulUpdateMessage = 'Update successful';

    // Invoices page

    // By date form
    this.generateByDateForm = '#form-generate-invoices-by-date';
    this.dateFromInput = `${this.generateByDateForm} #form_date_from`;
    this.dateToInput = `${this.generateByDateForm} #form_date_to`;
    this.generatePdfByDateButton = `${this.generateByDateForm} #generate-pdf-by-date-button`;

    // By order status form
    this.generateByStatusForm = '#form-generate-invoices-by-status';
    this.statusOrderStateSpan = `${this.generateByStatusForm} span.status-name`;
    this.generatePdfByStatusButton = `${this.generateByStatusForm} #generate-pdf-by-status-button`;

    // Invoice options form
    this.invoiceOptionsForm = '#form-invoices-options';
    this.invoiceOptionsStatusToggleInput = (toggle: number) => `#form_enable_invoices_${toggle}`;
    this.taxBreakdownStatusToggleInput = (toggle: number) => `#form_enable_tax_breakdown_${toggle}`;
    this.invoiceOptionStatusToggleInput = (toggle: number) => `#form_enable_product_images_${toggle}`;
    this.invoiceNumberInput = '#form_invoice_number';
    this.legalFreeTextInput = '#form_legal_free_text_1';
    this.footerTextInput = '#form_footer_text_1';
    this.saveInvoiceOptionsButton = `${this.invoiceOptionsForm} #save-invoices-options-button`;
    this.invoicePrefixInput = '#form_invoice_prefix_1';
    this.invoiceAddCurrentYearToggleInput = (toggle: number) => `#form_add_current_year_${toggle}`;
    this.optionYearPositionRadioButton = (id: number) => `#form_year_position_${id} + i`;
  }

  /*
  Methods
   */

  /**
   * Generate PDF by date and download it
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on date from input
   * @param dateTo {string} Value to set on date to input
   * @returns {Promise<string|null>}
   */
  async generatePDFByDateAndDownload(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<string|null> {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);

    return this.clickAndWaitForDownload(page, this.generatePdfByDateButton);
  }

  /**
   * Get message error after generate invoice by status fail
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on filter date from input
   * @param dateTo {string} Value to set on filter date to input
   * @returns {Promise<string>}
   */
  async generatePDFByDateAndFail(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<string> {
    await this.setValuesForGeneratingPDFByDate(page, dateFrom, dateTo);
    await page.locator(this.generatePdfByDateButton).click();

    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Set values to generate pdf by date
   * @param page {Page} Browser tab
   * @param dateFrom {string} Value to set on filter date from input
   * @param dateTo {string} Value to set on filter date to input
   * @returns {Promise<void>}
   */
  async setValuesForGeneratingPDFByDate(page: Page, dateFrom: string = '', dateTo: string = ''): Promise<void> {
    if (dateFrom) {
      await page.fill(this.dateFromInput, dateFrom);
      await page.fill(this.dateToInput, dateTo);
    }
  }

  /**
   * Choose order status to generate
   * @param page {Page} Browser tab
   * @param statusName {string} Status name to select
   * @returns {Promise<void>}
   */
  async chooseStatus(page: Page, statusName: string): Promise<void> {
    const statusLocator = page
      .locator(this.statusOrderStateSpan)
      .filter({hasText: statusName});

    if (await statusLocator.count() === 0) {
      throw new Error(`${statusName} was not found on list`);
    }

    await statusLocator.first().click();
  }

  /**
   * Generate PDF by status
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  generatePDFByStatusAndDownload(page: Page): Promise<string|null> {
    return this.clickAndWaitForDownload(page, this.generatePdfByStatusButton);
  }

  /**
   * Get message error after generate invoice by status fail
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async generatePDFByStatusAndFail(page: Page): Promise<string> {
    await page.locator(this.generatePdfByStatusButton).click();
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * Enable disable invoices
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable invoices
   * @returns {Promise<void>}
   */
  async enableInvoices(page: Page, enable: boolean = true): Promise<void> {
    await this.setChecked(page, this.invoiceOptionsStatusToggleInput(enable ? 1 : 0));
  }

  /**
   * Save invoice options
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveInvoiceOptions(page: Page): Promise<string> {
    await page.locator(this.saveInvoiceOptionsButton).click();
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Enable disable product image
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable product image in the invoice
   * @returns {Promise<void>}
   */
  async enableProductImage(page: Page, enable: boolean = true): Promise<void> {
    await this.setChecked(page, this.invoiceOptionStatusToggleInput(enable ? 1 : 0));
  }

  /**
   * Enable tax breakdown
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable tax breakdown in the invoice
   * @returns {Promise<void>}
   */
  async enableTaxBreakdown(page: Page, enable: boolean = true): Promise<void> {
    await this.setChecked(page, this.taxBreakdownStatusToggleInput(enable ? 1 : 0));
  }

  /**
   * Set invoiceNumber, LegalFreeText, footerText
   * @param page {Page} Browser tab
   * @param data {InvoiceData} Values to set on invoice option inputs
   * @returns {Promise<void>}
   */
  async setInputOptions(page: Page, data: InvoiceData): Promise<void> {
    await this.setValue(page, this.invoiceNumberInput, data.invoiceNumber);
    await this.setValue(page, this.legalFreeTextInput, data.legalFreeText);
    await this.setValue(page, this.footerTextInput, data.footerText);
  }

  /**
   * Enable add current year to invoice
   * @param page {Page} Browser tab
   * @param enable {boolean} True if we need to enable add current year to invoice
   * @returns {Promise<void>}
   */
  async enableAddCurrentYearToInvoice(page: Page, enable: boolean = true): Promise<void> {
    await this.setChecked(page, this.invoiceAddCurrentYearToggleInput(enable ? 1 : 0));
  }

  /**
   * Choose the position of the year
   * @param page {Page} Browser tab
   * @param id {number} Radio button id for position of the year date
   * @returns {Promise<void>}
   */
  async chooseInvoiceOptionsYearPosition(page: Page, id: number): Promise<void> {
    await page.locator(this.optionYearPositionRadioButton(id)).click();
  }

  /**
   * Edit invoice Prefix
   * @param page {Page} Browser tab
   * @param prefix {string} Prefix value to change
   * @returns {Promise<void>}
   */
  async changePrefix(page: Page, prefix: string): Promise<void> {
    await this.setValue(page, this.invoicePrefixInput, prefix);
  }
}

export default new Invoice();
