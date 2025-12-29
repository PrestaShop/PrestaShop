import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
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
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Change quantity from product page', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to the third product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 3);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle.toUpperCase()).to.contains(dataProducts.demo_6.name.toUpperCase());
    });

    it('should change the quantity by using the arrow \'Down\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'decrement', baseContext);

      await foHummingbirdProductPage.setQuantityByArrowUpDown(page, 1, 'down');

      const productQuantity = await foHummingbirdProductPage.getProductQuantity(page);
      expect(productQuantity).to.equal(1);
    });

    it('should change the quantity by using the arrow \'UP\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'incrementQuantity', baseContext);

      await foHummingbirdProductPage.setQuantityByArrowUpDown(page, 2, 'increment');

      const productQuantity = await foHummingbirdProductPage.getProductQuantity(page);
      expect(productQuantity).to.equal(2);
    });

    it('should click on add to cart button then on continue shopping button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnAddToCartButton', baseContext);

      await foHummingbirdProductPage.clickOnAddToCartButton(page);

      const isNotVisible = await foHummingbirdModalBlockCartPage.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });

    it('should set the quantity 0 and add to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButtonIsDisabled', baseContext);

      await foHummingbirdProductPage.setQuantity(page, 0);
      await foHummingbirdProductPage.clickOnAddToCartButton(page);

      const isNotVisible = await foHummingbirdModalBlockCartPage.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });

    it('should check the cart notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber1', baseContext);

      const notificationsNumber = await foHummingbirdProductPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(3);
    });

    it('should add quantity of the product by setting input value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityByInput', baseContext);

      await foHummingbirdProductPage.setQuantity(page, 12);
      await foHummingbirdProductPage.clickOnAddToCartButton(page);

      const isVisible = await foHummingbirdModalBlockCartPage.isBlockCartModalVisible(page);
      expect(isVisible).to.equal(true);
    });

    it('should click on continue shopping', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnContinueShopping2', baseContext);

      const isNotVisible = await foHummingbirdModalBlockCartPage.continueShopping(page);
      expect(isNotVisible).to.equal(true);
    });

    it('should check the cart notifications number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNotificationsNumber', baseContext);

      const notificationsNumber = await foHummingbirdProductPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.equal(15);
    });

    it('should go to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCart', baseContext);

      await foHummingbirdProductPage.clickOnHeaderLink(page, 'Cart');

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should remove product from shopping cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

      await foHummingbirdCartPage.deleteProduct(page, 1);

      const notificationNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.equal(0);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
