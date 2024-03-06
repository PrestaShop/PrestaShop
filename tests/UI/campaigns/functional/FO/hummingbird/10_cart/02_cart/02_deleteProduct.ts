// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import homePage from '@pages/FO/hummingbird/home';
import quickViewModal from '@pages/FO/hummingbird/modal/quickView';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_cart_cart_deleteProduct';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to Fo and add the first product to cart
- Increase/Decrease the product quantity by the touchSpin up/down
- Edit product quantity bu the input (3, -6, +6, 64, 'azerty', 2400, 0)
Post-condition:
- Uninstall hummingbird theme
*/
describe('FO - cart : Delete product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Delete product in cart page', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      await homePage.quickViewProduct(page, 1);
      await quickViewModal.addToCartByQuickView(page);
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

      const result = await homePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart1', baseContext);

      await homePage.quickViewProduct(page, 1);
      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    // @todo https://github.com/PrestaShop/hummingbird/pull/541
    it('should set the quantity 0 by the touchSpin', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity0ByTouchSpin', baseContext);

      await cartPage.setProductQuantity(page, 1, 0);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(1);
    });

    it.skip('should check the message "There are no more items in your cart"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoItemMessage2', baseContext);

      const message = await cartPage.getNoItemsInYourCartMessage(page);
      expect(message).to.equal(cartPage.noItemsInYourCartMessage);
    });

    it.skip('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await cartPage.goToHomePage(page);

      const result = await homePage.isHomePage(page);
      expect(result).to.equal(true);
    });

    it.skip('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart1', baseContext);

      await homePage.quickViewProduct(page, 1);
      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should set the quantity 0 in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity0', baseContext);

      await cartPage.editProductQuantity(page, 1, 0);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(1);
    });

    it.skip('should check the message "There are no more items in your cart"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoItemMessage3', baseContext);

      const message = await cartPage.getNoItemsInYourCartMessage(page);
      expect(message).to.equal(cartPage.noItemsInYourCartMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
