import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type BrowserContext,
  dataProducts,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_checkout_showDetails';

/*
Scenario:
- Add first and third product to cart
- Go to checkout page
- Click on show details
- Show all details
- Click on the product image
- Click on the product name
 */

describe('FO - Checkout : Show details', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add the first product to cart then close block cart modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

    await foClassicHomePage.quickViewProduct(page, 1);
    await foClassicModalQuickViewPage.addToCartByQuickView(page);

    const isModalClosed = await foClassicModalBlockCartPage.closeBlockCartModal(page);
    expect(isModalClosed).to.eq(true);
  });

  it('should add the third product to cart', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

    await foClassicLoginPage.goToHomePage(page);
    await foClassicHomePage.quickViewProduct(page, 3);
    await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, 2);
    await foClassicModalBlockCartPage.proceedToCheckout(page);

    const pageTitle = await foClassicCartPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
  });

  it('should proceed to checkout and go to checkout page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

    await foClassicCartPage.clickOnProceedToCheckout(page);

    const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
    expect(isCheckoutPage).to.eq(true);
  });

  it('should check the items number', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkItemsNumber', baseContext);

    const itemsNumber = await foClassicCheckoutPage.getItemsNumber(page);
    expect(itemsNumber).to.equal('3 items');
  });

  it('should click on \'Show details\' link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'showDetails', baseContext);

    const isProductsListVisible = await foClassicCheckoutPage.clickOnShowDetailsLink(page);
    expect(isProductsListVisible).to.eq(true);
  });

  it('should check the first product details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkFirstProductDetails', baseContext);
    const result = await foClassicCheckoutPage.getProductDetails(page, 1);
    await Promise.all([
      expect(result.image).to.contains(dataProducts.demo_1.coverImage),
      expect(result.name).to.equal(dataProducts.demo_1.name),
      expect(result.quantity).to.equal(1),
      expect(result.price).to.equal(dataProducts.demo_1.finalPrice),
    ]);

    const attributes = await foClassicCheckoutPage.getProductAttributes(page, 1);
    expect(attributes).to.equal('Size: S');
  });

  it('should check the second product details', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSecondProductDetails', baseContext);
    const result = await foClassicCheckoutPage.getProductDetails(page, 2);
    await Promise.all([
      expect(result.image).to.contains(dataProducts.demo_6.coverImage),
      expect(result.name).to.equal(dataProducts.demo_6.name),
      expect(result.quantity).to.equal(2),
      expect(result.price).to.equal(dataProducts.demo_6.combinations[0].price),
    ]);

    const attributes = await foClassicCheckoutPage.getProductAttributes(page, 2);
    expect(attributes).to.equal('Dimension: 40x60cm');
  });

  it('click on first product name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnFirstProductName', baseContext);

    page = await foClassicCheckoutPage.clickOnProductName(page, 1);

    const productInformation = await foClassicProductPage.getProductInformation(page);
    expect(productInformation.name).to.equal(dataProducts.demo_1.name);
  });

  it('should close the page and click on the first product image', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnFirstProductImage', baseContext);

    page = await foClassicProductPage.closePage(browserContext, page, 0);
    await foClassicCheckoutPage.clickOnProductImage(page, 1);

    const productInformation = await foClassicProductPage.getProductInformation(page);
    expect(productInformation.name).to.equal(dataProducts.demo_1.name);
  });
});
