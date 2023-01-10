// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import CustomerSettingsOptions from '@pages/BO/shopParameters/customerSettings/options';

// Import FO pages
import homePage from '@pages/FO/home';
import loginFOPage from '@pages/FO/login';

// Import data
import {DefaultCustomer} from '@data/demo/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_customers_redisplayCartAtLogin';

/*
Enable re-display cart at login
Login FO and add a product to the cart
Logout FO then Login and check that the cart is not empty
Disable re-display cart at login
Login FO and add a product to the cart
Logout FO then Login and check that the cart is empty
 */
describe('BO - Shop Parameters - Customer Settings : Enable/Disable re-display cart at login', async () => {
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

  it('should go to \'Shop parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.customerSettingsLink,
    );
    await customerSettingsPage.closeSfToolBar(page);

    const pageTitle = await customerSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', enable: true}},
    {args: {action: 'disable', enable: false}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} re-display cart at login`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}RedisplayCartAtLogin`,
        baseContext,
      );

      const result = await customerSettingsPage.setOptionStatus(
        page,
        CustomerSettingsOptions.OPTION_CART_LOGIN,
        test.args.enable,
      );
      await expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
    });

    it('should login FO and add the first product to the cart then logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToTheCart_${index}`, baseContext);

      // Go to FO
      page = await customerSettingsPage.viewMyShop(page);

      // Login FO
      await homePage.goToLoginPage(page);
      await loginFOPage.customerLogin(page, DefaultCustomer);

      const connected = await homePage.isCustomerConnected(page);
      await expect(connected, 'Customer is not connected in FO').to.be.true;

      // Add first product to the cart
      await homePage.goToHomePage(page);
      await homePage.addProductToCartByQuickView(page, 1, 1);
      await homePage.proceedToCheckout(page);

      // Check number of product in cart
      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.above(0);

      // Logout from FO
      await homePage.logout(page);
    });

    it('should login FO and check the cart then logout', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `loginFOAndCheckNotificationNumber_${index}`,
        baseContext,
      );

      // Login FO
      await homePage.goToLoginPage(page);
      await loginFOPage.customerLogin(page, DefaultCustomer);

      const connected = await homePage.isCustomerConnected(page);
      await expect(connected, 'Customer is not connected in FO').to.be.true;

      // Check number of product in cart
      const notificationsNumber = await homePage.getCartNotificationsNumber(page);

      if (test.args.enable) {
        await expect(notificationsNumber).to.be.above(0);
      } else {
        await expect(notificationsNumber).to.be.equal(0);
      }

      // Logout from FO
      await homePage.logout(page);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO_${index}`, baseContext);

      // Go back to BO
      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });
  });
});
