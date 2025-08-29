import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.customerSettingsLink,
    );
    await boCustomerSettingsPage.closeSfToolBar(page);

    const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
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

      const result = await boCustomerSettingsPage.setOptionStatus(
        page,
        boCustomerSettingsPage.OPTION_CART_LOGIN,
        test.args.enable,
      );
      expect(result).to.contains(boCustomerSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop_${index}`, baseContext);

      // Go to FO
      page = await boCustomerSettingsPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `loginFO_${index}`, baseContext);

      // Login FO
      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const connected = await foClassicHomePage.isCustomerConnected(page);
      expect(connected, 'Customer is not connected in FO').to.eq(true);
    });

    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `quickViewFirstProduct_${index}`, baseContext);

      // Add first product to the cart
      await foClassicHomePage.goToHomePage(page);
      await foClassicHomePage.quickViewProduct(page, 1);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToTheCart_${index}`, baseContext);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      // Check number of product in cart
      const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.above(0);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `logoutFO_${index}`, baseContext);

      // Logout from FO
      await foClassicHomePage.logout(page);

      const connected = await foClassicHomePage.isCustomerConnected(page);
      expect(connected, 'Customer is connected in FO').to.eq(false);
    });

    it('should login FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `loginFO_2_${index}`, baseContext);

      // Login FO
      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const connected = await foClassicHomePage.isCustomerConnected(page);
      expect(connected, 'Customer is not connected in FO').to.eq(true);
    });

    it('should check the cart then logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkNotificationNumber_${index}`, baseContext);

      // Check number of products in cart
      const notificationsNumber = await foClassicHomePage.getCartNotificationsNumber(page);

      if (test.args.enable) {
        expect(notificationsNumber).to.be.above(0);
        // Logout from FO
        await foClassicHomePage.logout(page);
      } else {
        expect(notificationsNumber).to.be.equal(0);
      }
    });

    if (test.args.enable) {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO_${index}`, baseContext);

        // Go back to BO
        page = await foClassicHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
      });
    }
  });
});
