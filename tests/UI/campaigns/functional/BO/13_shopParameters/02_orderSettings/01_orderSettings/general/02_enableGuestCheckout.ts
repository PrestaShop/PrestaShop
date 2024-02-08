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
import {homePage} from '@pages/FO/classic/home';
import productPage from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_general_enableGuestCheckout';

describe('BO - Shop Parameters - Order Settings : Enable/Disable guest checkout', async () => {
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
    expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
  });

  const tests = [
    {
      args: {
        action: 'disable', exist: false, tabName: 'Create an account', pwdRequired: true,
      },
    },
    {
      args: {
        action: 'enable', exist: true, tabName: 'Order as a guest', pwdRequired: false,
      },
    },
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} guest checkout`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `guestCheckout${index}`, baseContext);

      const result = await orderSettingsPage.setGuestCheckoutStatus(page, test.args.exist);
      expect(result).to.contains(orderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await orderSettingsPage.viewMyShop(page);
      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

      // Go to the first product page
      await homePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(index + 1);
    });

    it('should check active link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIfNoticeVisible${index}`, baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      // Check guest checkout
      const isNoticeVisible = await checkoutPage.getActiveLinkFromPersonalInformationBlock(page);
      expect(isNoticeVisible).to.be.equal(test.args.tabName);
    });

    it('should verify the guest checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkGuestCheckout${index}`, baseContext);

      const isPasswordRequired = await checkoutPage.isPasswordRequired(page);
      expect(isPasswordRequired).to.be.equal(test.args.pwdRequired);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      page = await checkoutPage.closePage(browserContext, page, 0);

      const pageTitle = await orderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(orderSettingsPage.pageTitle);
    });
  });
});
