// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import contactUsPage from '@pages/FO/hummingbird/contactUs';
import homePage from '@pages/FO/hummingbird/home';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

import {
  dataCustomers,
  foHummingbirdLoginPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdMyAccountPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_headerAndFooter_checkLinksInHeader';

/*
Go to FO
Check header links:
- Contact us
- Sign in
- My account( Customer name)
- Cart
- Sign out
- Logo
 */
describe('FO - Header and Footer : Check links in header page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest_1`);

  describe('Check links in header page', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should check \'Contact us\' header link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkContactUsHeaderLink', baseContext);

      // Check Contact us
      await homePage.clickOnHeaderLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(contactUsPage.pageTitle);
    });

    it('should check \'sign in\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSignInLink', baseContext);

      // Check sign in link
      await homePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in by default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      // Sign in
      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it('should check my account link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMyAccountLink', baseContext);

      await foHummingbirdLoginPage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should add a product to cart by quick view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdLoginPage.goToHomePage(page);
      // Add product to cart by quick view
      await homePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.setQuantityAndAddToCart(page, 3);

      // Close block cart modal
      const isQuickViewModalClosed = await blockCartModal.closeBlockCartModal(page);
      expect(isQuickViewModalClosed).to.eq(true);
    });

    it('should check \'Cart\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShoppingCartLink', baseContext);

      // Check cart link
      await homePage.clickOnHeaderLink(page, 'Cart');

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should go to home page and check the notification number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationNumber1', baseContext);

      await foHummingbirdLoginPage.goToHomePage(page);

      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationsNumber, 'Notification number is not equal to 3!').to.be.equal(3);
    });

    it('should check \'Sign out\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSignOutLink', baseContext);

      // Sign out
      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });

    it('should check that the cart is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationNumber2', baseContext);

      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationsNumber, 'The cart is not empty!').to.be.equal(0);
    });

    it('should check \'Logo\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLogoLink', baseContext);

      await homePage.clickOnHeaderLink(page, 'Logo', false);

      const pageTitle = await homePage.getPageTitle(page);
      expect(pageTitle).to.equal(homePage.pageTitle);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest_1`);
});
