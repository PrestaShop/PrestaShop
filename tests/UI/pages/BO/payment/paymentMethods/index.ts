import BOBasePage from '@pages/BO/BObasePage';
import {
  type FakerModule,
} from '@prestashop-core/ui-testing';

import type {Page} from 'playwright';

/**
 * BO Payment preferences page, contains texts, selectors and functions to use on the page.
 * @class
 * @extends BOBasePage
 */
class PaymentMethodsPage extends BOBasePage {
  public readonly pageTitle: string;

  private readonly tablePayments: string;

  private readonly tablePaymentsRow: string;

  private readonly tablePaymentsRowName: (name: string) => string;

  private readonly tablePaymentsBtnConfigure: (name: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use
   */
  constructor() {
    super();

    this.pageTitle = `Payment methods • ${global.INSTALL.SHOP_NAME}`;
    this.tablePayments = '.card-body .module-item-list';
    this.tablePaymentsRow = `${this.tablePayments} div.row`;
    this.tablePaymentsRowName = (name: string) => `${this.tablePaymentsRow}[data-name="${name}"]`;
    this.tablePaymentsBtnConfigure = (name: string) => `${this.tablePaymentsRowName(name)} div:nth-child(3) a.btn`;
  }

  /*
  Methods
   */
  /**
   * Returns the number of active payments
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCountActivePayments(page: Page): Promise<number> {
    return page.locator(this.tablePaymentsRow).count();
  }

  /**
   * Returns if the active payment has a "Configure" button
   * @param page {Page} Browser tab
   * @param module {FakerModule} Module
   * @returns {Promise<boolean>}
   */
  async hasConfigureButton(page: Page, module: FakerModule): Promise<boolean> {
    return (await page.locator(this.tablePaymentsBtnConfigure(module.tag)).count()) === 1;
  }

  /**
   * Click on the "Configure" button
   * @param page {Page} Browser tab
   * @param module {FakerModule} Module
   * @returns {Promise<void>}
   */
  async clickConfigureButton(page: Page, module: FakerModule): Promise<void> {
    await page.locator(this.tablePaymentsBtnConfigure(module.tag)).click();
  }
}

export default new PaymentMethodsPage();
