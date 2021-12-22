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
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');

const baseContext = 'functional_BO_shopParameters_orderSettings_general_minimumPurchaseTotalRequired';

let browserContext;
let page;

const newPurchaseTotalRequired = 100;
const defaultPurchaseTotalRequired = 0;

const alertMessage = `A minimum shopping cart total of €${newPurchaseTotalRequired}.00 (tax excl.)`
  + ' is required to validate your order.';

/*
Update minimum purchase total value
Go to FO and check the alert message
 */
describe('BO - Shop Parameters - Order Settings : Test minimum purchase total required in order '
  + 'to validate the order', async () => {
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
    {args: {value: newPurchaseTotalRequired, disable: true, alertMessage: true}},
    {args: {value: defaultPurchaseTotalRequired, disable: false, alertMessage: false}},
  ];

  tests.forEach((test, index) => {
    it('should update Minimum purchase total required in order to validate the order value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateMinimumPurchaseTotal_${index}`, baseContext);

      const result = await orderSettingsPage.setMinimumPurchaseRequiredTotal(page, test.args.value);
      await expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await orderSettingsPage.viewMyShop(page);

      // Change Fo language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
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
      await homePage.goToProductPage(page, 1);

      // Add the created product to the cart
      await productPage.addProductToTheCart(page);

      // Check proceed to checkout button enable/disable
      const isDisabled = await cartPage.isProceedToCheckoutButtonDisabled(page);
      await expect(isDisabled).to.equal(test.args.disable);

      // Check alert message
      const isAlertVisible = await cartPage.isAlertWarningForMinimumPurchaseVisible(page);
      await expect(isAlertVisible).to.equal(test.args.alertMessage);

      if (isAlertVisible) {
        const alertText = await cartPage.getAlertWarning(page);
        await expect(alertText).to.contains(alertMessage);
      }
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `BackToBO${index}`, baseContext);

      page = await cartPage.closePage(browserContext, page, 0);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });
  });
});
