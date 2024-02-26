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
import {homePage} from '@pages/FO/classic/home';
import {loginPage as loginFOPage} from '@pages/FO/classic/login';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';

// Import data
import Customers from '@data/demo/customers';

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
    expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', enable: true}},
    {args: {action: 'disable', enable: false}},
  ];

  tests.forEach((test, index: number) => {
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
      expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop_${index}`, baseContext);

      // Go to FO
      page = await customerSettingsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `loginFO_${index}`, baseContext);

      // Login FO
      await homePage.goToLoginPage(page);
      await loginFOPage.customerLogin(page, Customers.johnDoe);

      const connected = await homePage.isCustomerConnected(page);
      expect(connected, 'Customer is not connected in FO').to.eq(true);
    });

    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `quickViewFirstProduct_${index}`, baseContext);

      // Add first product to the cart
      await homePage.goToHomePage(page);
      await homePage.quickViewProduct(page, 1);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToTheCart_${index}`, baseContext);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      // Check number of product in cart
      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.above(0);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `logoutFO_${index}`, baseContext);

      // Logout from FO
      await homePage.logout(page);

      const connected = await homePage.isCustomerConnected(page);
      expect(connected, 'Customer is connected in FO').to.eq(false);
    });

    it('should login FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `loginFO_2_${index}`, baseContext);

      // Login FO
      await homePage.goToLoginPage(page);
      await loginFOPage.customerLogin(page, Customers.johnDoe);

      const connected = await homePage.isCustomerConnected(page);
      expect(connected, 'Customer is not connected in FO').to.eq(true);
    });

    it('should check the cart then logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkNotificationNumber_${index}`, baseContext);

      // Check number of products in cart
      const notificationsNumber = await homePage.getCartNotificationsNumber(page);

      if (test.args.enable) {
        expect(notificationsNumber).to.be.above(0);
        // Logout from FO
        await homePage.logout(page);
      } else {
        expect(notificationsNumber).to.be.equal(0);
      }
    });

    if (test.args.enable) {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO_${index}`, baseContext);

        // Go back to BO
        page = await homePage.closePage(browserContext, page, 0);

        const pageTitle = await customerSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
      });
    }
  });
});
