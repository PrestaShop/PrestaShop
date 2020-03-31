require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orderSettings_disableReorderingOption';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const FOLoginPage = require('@pages/FO/login');
const FOBasePage = require('@pages/FO/FObasePage');
const MyAccountPage = require('@pages/FO/myAccount');
const OrderHistoryPage = require('@pages/FO/orderHistory');
// Importing data
const {DefaultAccount} = require('@data/demo/customer');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    orderSettingsPage: new OrderSettingsPage(page),
    foLoginPage: new FOLoginPage(page),
    foBasePage: new FOBasePage(page),
    myAccountPage: new MyAccountPage(page),
    orderHistoryPage: new OrderHistoryPage(page),
  };
};

describe('Enable/Disable reordering option', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to Shop Parameters > Order Settings page
  loginCommon.loginBO();

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.orderSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.orderSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', status: true, reorderOption: false}},
    {args: {action: 'disable', status: false, reorderOption: true}},
  ];
  tests.forEach((test) => {
    it(`should ${test.args.action} reordering option`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}GuestCheckout`, baseContext);
      const result = await this.pageObjects.orderSettingsPage.setReorderOptionStatus(test.args.status);
      await expect(result).to.contains(this.pageObjects.orderSettingsPage.successfulUpdateMessage);
    });

    it('should go to FO and verify the reordering option', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkReorderingOption${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}`,
        baseContext,
      );
      // Click on view my shop
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      // Login FO
      await this.pageObjects.foBasePage.goToLoginPage();
      await this.pageObjects.foLoginPage.customerLogin(DefaultAccount);
      const isCustomerConnected = await this.pageObjects.foLoginPage.isCustomerConnected();
      await expect(isCustomerConnected).to.be.true;
      // Go to order history page
      await this.pageObjects.myAccountPage.goToHistoryAndDetailsPage();
      // Check reorder link
      const isReorderLinkVisible = await this.pageObjects.orderHistoryPage.isReorderLinkVisible();
      await expect(isReorderLinkVisible).to.be.equal(test.args.reorderOption);
      // Logout FO
      await this.pageObjects.foBasePage.logout();
      page = await this.pageObjects.orderHistoryPage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
});
