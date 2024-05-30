// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
import homePage from '@pages/FO/hummingbird/home';
import productPage from '@pages/FO/hummingbird/product';
import blockCartModal from '@pages/FO/hummingbird/modal/blockCart';
import cartPage from '@pages/FO/hummingbird/cart';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataProducts,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_productPage_productPage_changeQuantity';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to FO
- Go to the third product in the list
- Click up/down on quantity input
- Set quantity input (good/bad value)
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Product page : Change quantity', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Change quantity from product page', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to the third product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await homePage.goToProductPage(page, 3);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_6.name.toUpperCase());
    });

    it('should change the quantity by using the arrow \'Down\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'decrement', baseContext);

      await productPage.setQuantityByArrowUpDown(page, 1, 'down');

      const productQuantity = await productPage.getProductQuantity(page);
      expect(productQuantity).to.equal(1);
    });

    it('should change the quantity by using the arrow \'UP\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'incrementQuantity', baseContext);

      await productPage.setQuantityByArrowUpDown(page, 2, 'increment');

      const productQuantity = await productPage.getProductQuantity(page);
      expect(productQuantity).to.equal(2);
    });

    it('should click on add to cart button then on continue shopping button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnAddToCartButton', baseContext);

      await productPage.clickOnAddToCartButton(page);

      const isNotVisible = await blockCartModal.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });

    // @todo : https://github.com/PrestaShop/hummingbird/pull/600
    it('should set the quantity 0 and check that the add to cart button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButtonIsDisabled', baseContext);

      this.skip();
      await productPage.setQuantity(page, 0);

      const isButtonDisabled = await productPage.isAddToCartButtonEnabled(page);
      expect(isButtonDisabled).to.equal(false);
    });

    it('should add quantity of the product by setting input value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput', baseContext);

      // @todo : https://github.com/PrestaShop/hummingbird/issues/615
      await productPage.reloadPage(page);

      await productPage.setQuantity(page, 12);
      await productPage.clickOnAddToCartButton(page);

      const isVisible = await blockCartModal.isBlockCartModalVisible(page);
      expect(isVisible).to.equal(true);
    });

    it('should click on continue shopping and check that the modal is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping', baseContext);

      const isNotVisible = await blockCartModal.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });

    it('should check the cart notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

      const notificationsNumber = await productPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(14);
    });

    // @todo : https://github.com/PrestaShop/hummingbird/pull/600
    it('should set \'-24\' in the quantity input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput2', baseContext);

      this.skip();
      await productPage.setQuantity(page, '-24');

      const isButtonDisabled = await productPage.isAddToCartButtonEnabled(page);
      expect(isButtonDisabled).to.equal(false);
    });

    it('should set \'Prestashop\' in the quantity input and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput3', baseContext);

      // @todo : https://github.com/PrestaShop/hummingbird/issues/615
      await productPage.reloadPage(page);

      await productPage.setQuantity(page, 'Prestashop');
      await productPage.clickOnAddToCartButton(page);

      const errorAlert = await productPage.getWarningMessage(page);
      expect(errorAlert).to.equal('Null quantity.');
    });

    it('should remove product from shopping cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

      await productPage.goToCartPage(page);
      await cartPage.deleteProduct(page, 1);

      const notificationNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.equal(0);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
