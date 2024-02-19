// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';

// Import pages
import homePage from '@pages/FO/hummingbird/home';
import cartPage from '@pages/FO/hummingbird/cart';
import searchResultsPage from '@pages/FO/hummingbird/searchResults';

// Import data
import Products from '@data/demo/products';
import CartProductDetails from '@data/types/cart';
import {ProductAttribute} from '@data/types/product';

import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_productPage_quickView_addToCart';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
- Go to FO and add first product to cart by quick view
- Check product details in the cart modal
- Proceed to checkout and check product details in the cart
- Go to home page and search a customized product
- Check that add to cart button is disabled
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Product page - Quick view : Add to cart', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const checkProductDetails: CartProductDetails = {
    name: Products.demo_1.name,
    price: Products.demo_1.finalPrice,
    cartSubtotal: Products.demo_1.finalPrice,
    totalTaxIncl: Products.demo_1.finalPrice,
    quantity: 1,
    cartProductsCount: 1,
    cartShipping: 'Free',
  };

  const checkProductDetailsProducts: ProductAttribute[] = [
    {
      name: 'size',
      value: 'S',
    },
    {
      name: 'color',
      value: 'White',
    },
  ];

  // Pre-condition : Install Hummingbird
  //installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('../../admin-dev/hummingbird.zip');
  });

  describe('Add to cart', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should add first product to cart by quick view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToCartByQuickView', baseContext);

      const successMessage = await homePage.addProductToCartByQuickView(page, 1, 1);
      expect(successMessage).to.contains(homePage.successAddToCartMessage);
    });

    it('should check product details from cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetailsInCartModal', baseContext);

      const result = await homePage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.name).to.equal(checkProductDetails.name),
        expect(result.price).to.equal(checkProductDetails.price),
        expect(result.quantity).to.equal(checkProductDetails.quantity),
        expect(result.cartProductsCount).to.equal(checkProductDetails.cartProductsCount),
        expect(result.cartSubtotal).to.equal(checkProductDetails.price),
        expect(result.cartShipping).to.contains(checkProductDetails.cartShipping),
        expect(result.totalTaxIncl).to.equal(checkProductDetails.totalTaxIncl),
      ]);

      const productAttributesFromBlockCart = await homePage.getProductAttributesFromBlockCartModal(page);
      await Promise.all([
        expect(productAttributesFromBlockCart.length).to.equal(2),
        expect(productAttributesFromBlockCart[0].name).to.equal(checkProductDetailsProducts[0].name),
        expect(productAttributesFromBlockCart[0].value).to.equal(checkProductDetailsProducts[0].value),
        expect(productAttributesFromBlockCart[1].name).to.equal(checkProductDetailsProducts[1].name),
        expect(productAttributesFromBlockCart[1].value).to.equal(checkProductDetailsProducts[1].value),
      ]);
    });

    it('should proceed to checkout and check the cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCartPage', baseContext);

      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should check product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetailsInCartPage', baseContext);

      const result = await cartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_1.name),
        expect(result.regularPrice).to.equal(Products.demo_1.retailPrice),
        expect(result.price).to.equal(Products.demo_1.finalPrice),
        expect(result.discountPercentage).to.equal(`-${Products.demo_1.specificPrice.discount}%`),
        expect(result.image).to.contains(Products.demo_1.coverImage),
        expect(result.quantity).to.equal(checkProductDetails.quantity),
        expect(result.totalPrice).to.equal(checkProductDetails.totalTaxIncl),
      ]);

      const cartProductAttributes = await cartPage.getProductAttributes(page, 1);
      await Promise.all([
        expect(cartProductAttributes.length).to.equal(2),
        expect(cartProductAttributes[0].name).to.equal(checkProductDetailsProducts[0].name),
        expect(cartProductAttributes[0].value).to.equal(checkProductDetailsProducts[0].value),
        expect(cartProductAttributes[1].name).to.equal(checkProductDetailsProducts[1].name),
        expect(cartProductAttributes[1].value).to.equal(checkProductDetailsProducts[1].value),
      ]);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it(`should search for the product ${Products.demo_14.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForProductCustomized', baseContext);

      await homePage.searchProduct(page, Products.demo_14.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should quick view the product and check that Add to cart button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton', baseContext);

      await searchResultsPage.quickViewProduct(page, 1);

      const isDisabled = await homePage.isAddToCartButtonDisabled(page);
      expect(isDisabled).to.eq(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  //uninstallHummingbird(`${baseContext}_postTest`);
});
