require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const OrderSettingsPage = require('@pages/BO/shopParameters/orderSettings');
const FOLoginPage = require('@pages/FO/login');
const ProductPage = require('@pages/FO/product');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const CartPage = require('@pages/FO/cart');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_orderSettings_general_minimumPurchaseTotalRequired';

let browser;
let browserContext;
let page;

const newPurchaseTotalRequired = 100;
const defaultPurchaseTotalRequired = 0;

const alertMessage = `A minimum shopping cart total of €${newPurchaseTotalRequired}.00 (tax excl.)`
+ ' is required to validate your order.';

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    orderSettingsPage: new OrderSettingsPage(page),
    foLoginPage: new FOLoginPage(page),
    productPage: new ProductPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    cartPage: new CartPage(page),
  };
};

describe('Test minimum purchase total required in order to validate the order', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to Shop Parameters > Order Settings page
  loginCommon.loginBO();

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.orderSettingsLink,
    );

    await this.pageObjects.orderSettingsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.orderSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.orderSettingsPage.pageTitle);
  });

  const tests = [
    {args: {value: newPurchaseTotalRequired, disable: true, alertMessage: true}},
    {args: {value: defaultPurchaseTotalRequired, disable: false, alertMessage: false}},
  ];

  tests.forEach((test, index) => {
    it('should update Minimum purchase total required in order to validate the order value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateMinimumPurchaseTotal_${index}`, baseContext);

      const result = await this.pageObjects.orderSettingsPage.setMinimumPurchaseRequiredTotal(test.args.value);
      await expect(result).to.contains(this.pageObjects.orderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await this.pageObjects.orderSettingsPage.viewMyShop();
      this.pageObjects = await init();

      // Change Fo language
      await this.pageObjects.homePage.changeLanguage('en');

      const isHomePage = await this.pageObjects.homePage.isHomePage();
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should verify the minimum purchase total value', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkMinimumPurchaseTotal_${index}`,
        baseContext,
      );

      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);

      // Add the created product to the cart
      await this.pageObjects.productPage.addProductToTheCart();

      // Check proceed to checkout button enable/disable
      const isDisabled = await this.pageObjects.cartPage.isProceedToCheckoutButtonDisabled();
      await expect(isDisabled).to.equal(test.args.disable);

      // Check alert message
      const isAlertVisible = await this.pageObjects.cartPage.isAlertWarningForMinimumPurchaseVisible();
      await expect(isAlertVisible).to.equal(test.args.alertMessage);

      if (isAlertVisible) {
        const alertText = await this.pageObjects.cartPage.getAlertWarning();
        await expect(alertText).to.contains(alertMessage);
      }
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `BackToBO${index}`, baseContext);

      page = await this.pageObjects.cartPage.closePage(browserContext, 0);
      this.pageObjects = await init();

      const pageTitle = await this.pageObjects.orderSettingsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.orderSettingsPage.pageTitle);
    });
  });
});
