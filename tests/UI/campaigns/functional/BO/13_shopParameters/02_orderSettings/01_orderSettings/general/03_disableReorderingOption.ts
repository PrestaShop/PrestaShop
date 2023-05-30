// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import orderSettingsPage from '@pages/BO/shopParameters/orderSettings';
// Import FO pages
import {loginPage as foLoginPage} from '@pages/FO/login';
import {homePage} from '@pages/FO/home';
import {myAccountPage} from '@pages/FO/myAccount';
import orderHistoryPage from '@pages/FO/myAccount/orderHistory';

// Import data
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_general_disableReorderingOption';

/*
Enable/disable reordering option
Check reordering option in FO (Go to history page and check reodering link)
 */
describe('BO - Shop Parameters - Order Settings : Enable/Disable reordering option', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.orderSettingsLink,
    );
    await orderSettingsPage.closeSfToolBar(page);

    const pageTitle = await orderSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', status: true, reorderOption: false}},
    {args: {action: 'disable', status: false, reorderOption: true}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} reordering option`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `setReorderingOption${index}`, baseContext);

      const result = await orderSettingsPage.setReorderOptionStatus(page, test.args.status);
      await expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await orderSettingsPage.viewMyShop(page);
      // Change language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should verify the reordering option', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkReorderingOption${index}`, baseContext);

      // Login FO
      await homePage.goToLoginPage(page);
      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;

      // Go to order history page
      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToHistoryAndDetailsPage(page);

      // Check reorder link
      const isReorderLinkVisible = await orderHistoryPage.isReorderLinkVisible(page);
      await expect(isReorderLinkVisible).to.be.equal(test.args.reorderOption);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Logout FO
      await orderHistoryPage.logout(page);
      page = await orderHistoryPage.closePage(browserContext, page, 0);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });
  });
});
