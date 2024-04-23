// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import foLoginPage from '@pages/FO/hummingbird/login';
import foCreateAccountPage from '@pages/FO/hummingbird/myAccount/add';

import {
  // Import data
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to create account in FO
 * @param customerData {FakerCustomer} Data to set when creating the account
 * @param baseContext {string} String to identify the test
 */
function createAccountTest(customerData: FakerCustomer, baseContext: string = 'commonTests-createAccountTest'): void {
  describe('PRE-TEST: Create account on FO', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await homePage.goToLoginPage(page);
      await foLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await foCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foCreateAccountPage.goToHomePage(page);
      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });
}

export default createAccountTest;
