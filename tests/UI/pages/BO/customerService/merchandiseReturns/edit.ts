import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Edit merchandise returns page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class EditMerchandiseReturns extends BOBasePage {
  public readonly pageTitle: string;

  private readonly status: string;

  private readonly productsTableRow: (row: number) => string;

  private readonly productsTableDeleteColumn: (row: number) => string;

  private readonly continueButton: string;

  private readonly cancelButton: string;

  private readonly orderReturnSaveButton: string;

  private readonly orderReturnCancelButton: string;

  private readonly saveAndStayButton: string;

  private readonly fileName: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on edit merchandise return page
   */
  constructor() {
    super();

    this.pageTitle = 'Merchandise Returns > Edit •';

    // Selectors
    this.status = '#state';
    this.productsTableRow = (row: number) => `table tbody tr:nth-child(${row})`;
    this.productsTableDeleteColumn = (row: number) => `${this.productsTableRow(row)} td a.btn-default`;
    this.orderReturnSaveButton = '#order_return_form_submit_btn';
    this.orderReturnCancelButton = '#order_return_form_cancel_btn';
    this.saveAndStayButton = 'button[name=submitAddorder_returnAndStay]';
    this.fileName = '#fieldset_0 div.form-wrapper div:nth-child(8) div p:nth-child(1)';
    // Selectors in security page
    this.continueButton = 'body div.container div.action-container a.btn-continue';
    this.cancelButton = 'body div.container div.action-container a.btn-cancel';
  }

  /*
    Methods
  */
  /**
   * Set merchandise return status
   * @param page {Page} Browser tab
   * @param status {string} Status to select
   * @param saveAndStay {boolean} True if we need to click on save and stay button
   * @returns {Promise<string>}
   */
  async setStatus(page: Page, status: string, saveAndStay: boolean = false): Promise<string> {
    await this.selectByVisibleText(page, this.status, status);
    if (saveAndStay) {
      await this.clickAndWaitForURL(page, this.saveAndStayButton);
    } else {
      await this.clickAndWaitForURL(page, this.orderReturnSaveButton);
    }
    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Get file name
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getFileName(page: Page): Promise<string> {
    return this.getTextContent(page, this.fileName);
  }

  /**
   * Download PDF
   * @param page {Page} Browser tab
   * @returns {Promise<string | null>}
   */
  async downloadPDF(page: Page): Promise<string | null> {
    return this.clickAndWaitForDownload(page, `${this.fileName} a`);
  }

  /**
   * Click on cancel button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnCancelButton(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.orderReturnCancelButton);
    await this.clickAndWaitForURL(page, this.orderReturnCancelButton);
  }

  /**
   * Click on delete last product button
   * @param page {Page} Browser tab
   * @param row {number} Row in products table
   * @returns {Promise<string>}
   */
  async clickOnDeleteLastProductButton(page: Page, row: number = 1): Promise<string> {
    await this.clickAndWaitForURL(page, this.productsTableDeleteColumn(row));

    return this.getTextContent(page, this.alertBlock);
  }

  /**
   * Delete product
   * @param page {Page} Browser tab
   * @param row {number} Row in products table
   * @param understandTheRisk {boolean} True if you need to click on understand the risk button
   * @returns {Promise<string>}
   */
  async deleteProduct(page: Page, row: number = 1, understandTheRisk: boolean = true): Promise<string> {
    await this.clickAndWaitForURL(page, this.productsTableDeleteColumn(row));
    if (understandTheRisk) {
      await this.clickAndWaitForURL(page, this.continueButton);

      return this.getTextContent(page, this.alertBlock);
    }
    await this.clickAndWaitForURL(page, this.cancelButton);

    return this.getPageTitle(page);
  }
}

export default new EditMerchandiseReturns();
