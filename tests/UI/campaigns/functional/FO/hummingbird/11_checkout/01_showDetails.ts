import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_checkout_showDetails';

/*
Pre-condition:
- Install the theme hummingbird
Scenario:
- Add first and third product to cart
- Go to checkout page
- Click on show details
- Show all details
- Click on the product image
- Click on the product name
Post-condition:
- Uninstall the theme hummingbird
 */

describe('FO - Checkout : Show details', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Show details', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to cart then close block cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);
      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);
      await foHummingbirdModalBlockCartPage.closeBlockCartModal(page);

      const isModalVisible = await foHummingbirdModalBlockCartPage.isBlockCartModalVisible(page);
      expect(isModalVisible).to.eq(false);
    });

    it('should add the third product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foHummingbirdLoginPage.goToHomePage(page);
      await foHummingbirdHomePage.quickViewProduct(page, 3);
      await foHummingbirdModalQuickViewPage.setQuantityAndAddToCart(page, 2);
      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should proceed to checkout and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should check the items number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkItemsNumber', baseContext);

      const itemsNumber = await foHummingbirdCheckoutPage.getItemsNumber(page);
      expect(itemsNumber).to.equal('3 items');
    });

    it('should click on \'Show details\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'showDetails', baseContext);

      const isProductsListVisible = await foHummingbirdCheckoutPage.clickOnShowDetailsLink(page);
      expect(isProductsListVisible).to.eq(true);
    });

    it('should check the first product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProductDetails', baseContext);
      const result = await foHummingbirdCheckoutPage.getProductDetails(page, 1);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_1.coverImage),
        expect(result.name).to.equal(dataProducts.demo_1.name),
        expect(result.quantity).to.equal(1),
        expect(result.price).to.equal(dataProducts.demo_1.finalPrice),
      ]);

      const attributes = await foHummingbirdCheckoutPage.getProductAttributes(page, 1);
      expect(attributes).to.equal('Size: S');
    });

    it('should check the second product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductDetails', baseContext);
      const result = await foHummingbirdCheckoutPage.getProductDetails(page, 2);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_6.coverImage),
        expect(result.name).to.equal(dataProducts.demo_6.name),
        expect(result.quantity).to.equal(2),
        // @todo : https://github.com/PrestaShop/hummingbird/issues/865
        //expect(result.price).to.equal(dataProducts.demo_6.combinations[0].price),
      ]);

      const attributes = await foHummingbirdCheckoutPage.getProductAttributes(page, 2);
      expect(attributes).to.equal('Dimension: 40x60cm');
    });

    it('click on first product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnFirstProductName', baseContext);

      page = await foHummingbirdCheckoutPage.clickOnProductName(page, 1);

      const productInformation = await foHummingbirdProductPage.getProductInformation(page);
      expect(productInformation.name).to.equal(dataProducts.demo_1.name);
    });

    it('should close the page and click on the first product image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnFirstProductImage', baseContext);

      page = await foHummingbirdProductPage.closePage(browserContext, page, 0);
      await foHummingbirdCheckoutPage.clickOnProductImage(page, 1);

      const productInformation = await foHummingbirdProductPage.getProductInformation(page);
      expect(productInformation.name).to.equal(dataProducts.demo_1.name);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
