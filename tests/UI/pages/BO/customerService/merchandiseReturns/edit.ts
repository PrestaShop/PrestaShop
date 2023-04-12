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

  private readonly saveButton: string;

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
    this.saveButton = '#order_return_form_submit_btn';
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
   * @returns {Promise<string>}
   */
  async setStatus(page: Page, status: string): Promise<string> {
    await this.selectByVisibleText(page, this.status, status);
    await this.waitForSelectorAndClick(page, this.saveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Click on delete last product button
   * @param page {Page} Browser tab
   * @param row {number} Row in products table
   * @returns {Promise<string>}
   */
  async clickOnDeleteLastProductButton(page: Page, row: number = 1): Promise<string> {
    await this.clickAndWaitForNavigation(page, this.productsTableDeleteColumn(row));

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
    await this.clickAndWaitForNavigation(page, this.productsTableDeleteColumn(row));
    if (understandTheRisk) {
      await this.clickAndWaitForNavigation(page, this.continueButton);

      return this.getTextContent(page, this.alertBlock);
    }
    await this.clickAndWaitForNavigation(page, this.cancelButton);

    return this.getPageTitle(page);
  }
}

export default new EditMerchandiseReturns();
