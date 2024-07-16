// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  foHummingbirdHomePage,
  foHummingbirdModalQuickViewPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_cart_cart_deleteProduct';

describe('FO - cart : Delete product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableHummingbird(`${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Delete product in cart page', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should click on remove product link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.equal(0);
    });

    it('should check the message "There are no more items in your cart"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoItemMessage', baseContext);

      const message = await cartPage.getNoItemsInYourCartMessage(page);
      expect(message).to.equal(cartPage.noItemsInYourCartMessage);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await cartPage.goToHomePage(page);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart1', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should set the quantity 0 by the touchSpin', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity0ByTouchSpin', baseContext);

      await cartPage.setProductQuantity(page, 1, 0);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(0);
    });

    it('should check the message "There are no more items in your cart"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoItemMessage2', baseContext);

      const message = await cartPage.getNoItemsInYourCartMessage(page);
      expect(message).to.equal(cartPage.noItemsInYourCartMessage);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage2', baseContext);

      await cartPage.goToHomePage(page);

      const result = await foHummingbirdHomePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart3', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should set the quantity 0 in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity0', baseContext);

      await cartPage.editProductQuantity(page, 1, 0);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(0);
    });

    it.skip('should check the message "There are no more items in your cart"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoItemMessage3', baseContext);

      const message = await cartPage.getNoItemsInYourCartMessage(page);
      expect(message).to.equal(cartPage.noItemsInYourCartMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest`);
});
