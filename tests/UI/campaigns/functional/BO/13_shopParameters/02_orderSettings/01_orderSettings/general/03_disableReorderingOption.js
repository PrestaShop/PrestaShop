require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const orderSettingsPage = require('@pages/BO/shopParameters/orderSettings');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const homePage = require('@pages/FO/home');
const myAccountPage = require('@pages/FO/myAccount');
const orderHistoryPage = require('@pages/FO/myAccount/orderHistory');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_shopParameters_orderSettings_disableReorderingOption';

let browserContext;
let page;

/*
Enable/disable reordering option
Check reordering option in FO (Go to history page and check reodering link)
 */
describe('Enable/Disable reordering option', async () => {
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

  tests.forEach((test, index) => {
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
      await foLoginPage.customerLogin(page, DefaultCustomer);

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
