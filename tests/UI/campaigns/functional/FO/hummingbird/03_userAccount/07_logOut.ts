// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import homePage from '@pages/FO/hummingbird/home';

import {
  dataCustomers,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_userAccount_logOut';

describe('FO - User Account : LogOut', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Logout in FO', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should logIn', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

      await homePage.goToLoginPage(page);
      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should logOut with link in the footer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutWithLinkAtAccountPage', baseContext);

      await foHummingbirdMyAccountPage.logout(page);

      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest`);
});
