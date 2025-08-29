// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boOrderSettingsPage,
  type BrowserContext,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_orderSettings_orderSettings_general_enableGuestCheckout';

describe('BO - Shop Parameters - Order Settings : Enable/Disable guest checkout', async () => {
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

      const result = await boOrderSettingsPage.setGuestCheckoutStatus(page, test.args.exist);
      expect(result).to.contains(boOrderSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // Click on view my shop
      page = await boOrderSettingsPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);

      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(index + 1);
    });

    it('should check active link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIfNoticeVisible${index}`, baseContext);

      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Check guest checkout
      const isNoticeVisible = await foClassicCheckoutPage.getActiveLinkFromPersonalInformationBlock(page);
      expect(isNoticeVisible).to.be.equal(test.args.tabName);
    });

    it('should verify the guest checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkGuestCheckout${index}`, baseContext);

      const isPasswordRequired = await foClassicCheckoutPage.isPasswordRequired(page);
      expect(isPasswordRequired).to.be.equal(test.args.pwdRequired);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrderSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrderSettingsPage.pageTitle);
    });
  });
});
