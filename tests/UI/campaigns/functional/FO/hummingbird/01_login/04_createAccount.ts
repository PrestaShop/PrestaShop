// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';
import createAccountPage from '@pages/FO/hummingbird/myAccount/add';

import {
  FakerCustomer,
  foHummingbirdLoginPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_login_createAccount';

describe('FO - Login : Create account', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const customerData: FakerCustomer = new FakerCustomer();

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create account', () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foHummingbirdLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await createAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(createAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await createAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Created customer is not connected!').to.eq(true);
    });

    it('should check if the page is redirected to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isHomePage', baseContext);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to redirect to FO home page!').to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });
  });

  // Post-condition: Delete created customer account from BO
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest_2`);
});
