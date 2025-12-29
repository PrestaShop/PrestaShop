import testContext from '@utils/testContext';
import {expect} from 'chai';

import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  type CartProductDetails,
  dataProducts,
  foHummingbirdCartPage,
  foHummingbirdHomePage,
  foHummingbirdModalBlockCartPage,
  foHummingbirdModalQuickViewPage,
  foHummingbirdSearchResultsPage,
  type Page,
  type ProductAttribute,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    name: dataProducts.demo_1.name,
    price: dataProducts.demo_1.finalPrice,
    cartSubtotal: dataProducts.demo_1.finalPrice,
    totalTaxIncl: dataProducts.demo_1.finalPrice,
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
  enableTheme('hummingbird', `${baseContext}_preTest`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Add to cart', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should add first product to cart by quick view', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToCartByQuickView', baseContext);

      await foHummingbirdHomePage.quickViewProduct(page, 1);

      await foHummingbirdModalQuickViewPage.addToCartByQuickView(page);

      const successMessage = await foHummingbirdModalBlockCartPage.getBlockCartModalTitle(page);
      expect(successMessage).to.contains(foHummingbirdHomePage.successAddToCartMessage);
    });

    it('should check product details from cart modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetailsInCartModal', baseContext);

      const result = await foHummingbirdModalBlockCartPage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.name).to.equal(checkProductDetails.name),
        expect(result.price).to.equal(checkProductDetails.price),
        expect(result.quantity).to.equal(checkProductDetails.quantity),
        expect(result.cartProductsCount).to.equal(checkProductDetails.cartProductsCount),
        expect(result.cartSubtotal).to.equal(checkProductDetails.price),
        expect(result.cartShipping).to.contains(checkProductDetails.cartShipping),
        expect(result.totalTaxIncl).to.equal(checkProductDetails.totalTaxIncl),
      ]);

      const productAttributesFromBlockCart = await foHummingbirdModalBlockCartPage.getProductAttributesFromBlockCartModal(page);
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

      await foHummingbirdModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should check product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetailsInCartPage', baseContext);

      const result = await foHummingbirdCartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name).to.equal(dataProducts.demo_1.name),
        expect(result.regularPrice).to.equal(dataProducts.demo_1.retailPrice),
        expect(result.price).to.equal(dataProducts.demo_1.finalPrice),
        expect(result.discountPercentage).to.equal(`-${dataProducts.demo_1.specificPrice.discount}%`),
        expect(result.image).to.contains(dataProducts.demo_1.coverImage),
        expect(result.quantity).to.equal(checkProductDetails.quantity),
        expect(result.totalPrice).to.equal(checkProductDetails.totalTaxIncl),
      ]);

      const cartProductAttributes = await foHummingbirdCartPage.getProductAttributes(page, 1);
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

      await foHummingbirdHomePage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it(`should search for the product ${dataProducts.demo_14.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForProductCustomized', baseContext);

      await foHummingbirdHomePage.searchProduct(page, dataProducts.demo_14.name);

      const pageTitle = await foHummingbirdSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdSearchResultsPage.pageTitle);
    });

    it('should quick view the product and check that Add to cart button is disabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddToCartButton', baseContext);

      await foHummingbirdSearchResultsPage.quickViewProduct(page, 1);

      const isDisabled = await foHummingbirdModalQuickViewPage.isAddToCartButtonDisabled(page);
      expect(isDisabled).to.eq(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
