// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boOrderSettingsPage,
  type BrowserContext,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_general_minimumPurchaseTotalRequired';

/*
Update minimum purchase total value
Go to FO and check the alert message
 */
describe('BO - Shop Parameters - Order Settings : Test minimum purchase total required in order to validate the'
  + ' order', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const newPurchaseTotalRequired: number = 100;
  const defaultPurchaseTotalRequired: number = 0;
  const alertMessage: string = `A minimum shopping cart total of €${newPurchaseTotalRequired}.00 (tax excl.) is required`
    + ' to validate your order.';

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

  it('should go to \'Shop Parameters > Order Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.orderSettingsLink,
    );
    await boOrderSettingsPage.closeSfToolBar(page);

    const pageTitle = await boOrderSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
  });

  const tests = [
    {args: {value: newPurchaseTotalRequired, disable: true, alertMessage: true}},
    {args: {value: defaultPurchaseTotalRequired, disable: false, alertMessage: false}},
  ];

  tests.forEach((test, index: number) => {
    it('should update Minimum purchase total required in order to validate the order value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateMinimumPurchaseTotal_${index}`, baseContext);

      const result = await boOrderSettingsPage.setMinimumPurchaseRequiredTotal(page, test.args.value);
      expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await boOrderSettingsPage.viewMyShop(page);

      // Change Fo language
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToCart_${index}`, baseContext);

      // Go to the first product page
      await foHummingbirdHomePage.goToProductPage(page, 1);

      // Add the created product to the cart
      await foHummingbirdProductPage.addProductToTheCart(page);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(index + 1);
    });

    it('should verify the minimum purchase total value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkMinimumPurchaseTotal_${index}`, baseContext);

      // Check proceed to checkout button enable/disable
      const isDisabled = await foHummingbirdCartPage.isProceedToCheckoutButtonDisabled(page);
      expect(isDisabled).to.equal(test.args.disable);

      // Check alert message
      const isAlertVisible = await foHummingbirdCartPage.isAlertWarningForMinimumPurchaseVisible(page);
      expect(isAlertVisible).to.equal(test.args.alertMessage);

      if (isAlertVisible) {
        const alertText = await foHummingbirdCartPage.getAlertWarning(page);
        expect(alertText).to.contains(alertMessage);
      }
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `BackToBO${index}`, baseContext);

      page = await foHummingbirdCartPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });
  });
});
