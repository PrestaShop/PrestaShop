// Import FO pages
import {MyAccountPage} from '@pages/FO/classic/myAccount/index';
import type {Page} from 'playwright';

/**
 * My account page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MyAccount extends MyAccountPage {
  private readonly accountHistoryLinkLeftMenu: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on my account page
   */
  constructor() {
    super('hummingbird');

    this.orderSlipsLink = '.account-menu #order-slips__link';
    this.logoutFooterLink = '#my-account .account-menu .account-menu--signout';
    this.accountHistoryLinkLeftMenu = '#history__link';
  }

  /**
   * Go to order history page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnOrderHistoryAndDetailsLeftMenu(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.accountHistoryLinkLeftMenu);
  }
}

export default new MyAccount();
