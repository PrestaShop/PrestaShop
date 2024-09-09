import {type Page} from 'playwright';
import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

/**
 * Module configuration page for module : psgdpr, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsGdprTabDataConfigPage extends ModuleConfiguration {
  public readonly messageCustomerDataDeleted: string; 

  private readonly compliantModuleList: string;

  private readonly compliantModuleListItem: string;

  private readonly customerSearchBlock: string;

  private readonly customerSearchInput: string;

  private readonly customerSearchResultCard: string;

  private readonly customerSearchResultCardNth: (nth: number) => string;

  private readonly customerSearchResultCardBody: (nth: number) => string;

  private readonly customerSearchResultCardRemoveBtn: (nth: number) => string;

  private readonly customerSearchNoResultBlock: string;

  private readonly modalRemove: string;

  private readonly modalRemoveCancelBtn: string;

  private readonly modalRemoveConfirmBtn: string;

  private readonly modalRemoveText: string;

  /**
   * @constructs
   */
  constructor() {
    super();

    this.messageCustomerDataDeleted = 'The customer\'s data has been successfully deleted!';

    this.compliantModuleList = 'div.registered-modules';
    this.compliantModuleListItem = `${this.compliantModuleList} div.module-card`;
    this.customerSearchBlock = '#customerSearchBlock';
    this.customerSearchInput = `${this.customerSearchBlock} #search input.form-control`;
    this.customerSearchResultCard = `${this.customerSearchBlock} .customerCards div.customerCard`;
    this.customerSearchResultCardNth = (nth: number) => `${this.customerSearchResultCard}:nth-child(${nth})`;
    this.customerSearchResultCardBody = (nth: number) => `${this.customerSearchResultCardNth(nth)} div.panel-body`;
    this.customerSearchResultCardRemoveBtn = (nth: number) => `${this.customerSearchResultCardNth(nth)} div.panel-footer button`;
    this.customerSearchNoResultBlock = `${this.customerSearchBlock} article.alert-warning p:nth-child(1)`;

    // Modal Remove
    this.modalRemove = '.swal-overlay--show-modal';
    this.modalRemoveCancelBtn = `${this.modalRemove} .swal-modal .swal-footer .swal-button-container .swal-button--cancel`;
    this.modalRemoveConfirmBtn = `${this.modalRemove} .swal-modal .swal-footer .swal-button-container .swal-button--confirm`;
    this.modalRemoveText = `${this.modalRemove} .swal-modal .swal-text`;
  }

  /**
   * Returns the number of compliant modules
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberCompliantModules(page: Page): Promise<number> {
    return page.locator(this.compliantModuleListItem).count();
  }

  /**
   * Returns the number of compliant modules
   * @param page {Page} Browser tab
   * @param value {string} Customer name or Email
   * @returns {Promise<number>}
   */
  async searchCustomerData(page: Page, value: string): Promise<void> {
    await page.locator(this.customerSearchInput).fill(value);
  }

  /**
   * Returns if there is customer data
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async hasCustomerData(page: Page): Promise<boolean> {
    return (await page.locator(this.customerSearchNoResultBlock).count() === 0);
  }

  /**
   * Returns the number of customer data results
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async getNumberCustomerDataResults(page: Page): Promise<number> {
    return page.locator(this.customerSearchResultCard).count();
  }

  /**
   * Click on a result card
   * @param page {Page} Browser tab
   * @param nth {number} Index of the card (index 1-based)
   * @returns {Promise<boolean>}
   */
  async clickResultCard(page: Page, nth: number): Promise<void> {
    await page.locator(this.customerSearchResultCardNth(nth)).click();
    await this.elementVisible(page, this.customerSearchResultCardBody(nth))
  }

  /**
   * Click on "Remove data"
   * @param page {Page} Browser tab
   * @param nth {number} Index of the card (index 1-based)
   * @returns {Promise<string|null>}
   */
  async clickResultRemoveData(page: Page, nth: number, cancel: boolean = false): Promise<string|null> {
    await page.locator(this.customerSearchResultCardRemoveBtn(nth)).click();
    if (cancel) {
      await page.locator(this.modalRemoveCancelBtn).click();

      return null;
    }
    await page.locator(this.modalRemoveConfirmBtn).click();

    return page.locator(this.modalRemoveText).textContent();
  }

  /**
   * Click on "Remove data"
   * @param page {Page} Browser tab
   * @param nth {number} Index of the card (index 1-based)
   * @returns {Promise<string|null>}
   */
  async isModalRemoveDataVisible(page: Page): Promise<boolean> {
    return page.locator(this.modalRemove).isVisible();
  }
}

export default new PsGdprTabDataConfigPage();
