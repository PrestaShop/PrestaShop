import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  FakerCustomer,
  foHummingbirdCreateAccountPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);
      await foHummingbirdLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foHummingbirdCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await foHummingbirdCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foHummingbirdHomePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foHummingbirdCreateAccountPage.goToHomePage(page);
      await foHummingbirdHomePage.logout(page);

      const isCustomerConnected = await foHummingbirdHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });
}

export default createAccountTest;
