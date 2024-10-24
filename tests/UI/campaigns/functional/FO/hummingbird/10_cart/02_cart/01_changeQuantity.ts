// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableHummingbird, disableHummingbird} from '@commonTests/BO/design/hummingbird';

// Import FO pages
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';

import {expect} from 'chai';
import {
  type BrowserContext,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdModalQuickViewPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_cart_cart_changeQuantity';
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
describe('FO - cart : Change quantity', async () => {
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

  describe('Change quantity', async () => {
    it('should go to FO', async function () {
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

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should increase the product quantity by the touchSpin up to 5', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'increaseQuantity5', baseContext);

      const quantity = await foHummingbirdCartPage.setProductQuantity(page, 1, 5);
      expect(quantity).to.equal(5);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(5);
    });

    it('should decrease the product quantity by the touchSpin down to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'decreaseQuantity2', baseContext);

      const quantity = await foHummingbirdCartPage.setProductQuantity(page, 1, 2);
      expect(quantity).to.equal(2);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(2);
    });

    it('should set the quantity 3 in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity3', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 1, 3);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(3);
    });

    it('should set the quantity -6 in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity-6', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 1, -6);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(0);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage2', baseContext);

      await foHummingbirdCartPage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.equal(true);
    });

    it('should add the first product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart2', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should set the quantity +6 in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity+6', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 1, +6);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(6);
    });

    it('should set the quantity 64 in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity64', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 1, 64);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(64);
    });

    it('should set \'azerty\' in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAZERTY', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 1, 'azerty');

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(64);
    });

    it('should set the quantity 2400 in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity2400', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 1, 2400);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(300);
    });

    it('should check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      const alertText = await foHummingbirdCartPage.getNotificationMessage(page);
      expect(alertText).to.contains(foHummingbirdCartPage.errorNotificationForProductQuantity);
    });

    it('should set the quantity 3 in the input without validation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantityWithoutValidation', baseContext);

      await foHummingbirdCartPage.setQuantity(page, 1, 3);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(300);
    });

    it('should set the quantity 0 in the input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setQuantity', baseContext);

      await foHummingbirdCartPage.editProductQuantity(page, 1, 0);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(0);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableHummingbird(`${baseContext}_postTest`);
});
