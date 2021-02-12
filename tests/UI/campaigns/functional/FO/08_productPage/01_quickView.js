require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');

// Importing pages
const homePage = require('@pages/FO/home');
const cartPage = require('@pages/FO/cart');
const productPage = require('@pages/FO/product');
const boDashboardPage = require('@pages/BO/dashboard');
const boProductsPage = require('@pages/BO/catalog/products');
const boAddProductPage = require('@pages/BO/catalog/products/add');
const searchResultsPage = require('@pages/FO/searchResults');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_productPage_quickView';

// Import data
const {customCartData} = require('@data/FO/cart');
const {firstProductData} = require('@data/FO/product');
const ProductFaker = require('@data/faker/product');

let browserContext;
let page;
const combination = new ProductFaker({size: 'M', color: 'Black', quantity: 4});
const totalPrice = 91.78;
const productToCreate = {
  type: 'Standard product',
  productHasCombinations: false,
};
const productData = new ProductFaker(productToCreate);

/*

 */

describe('Product quick view', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  describe('Add to cart by quick view', async () => {
    it('should add product to cart by quick view and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToCartByQuickView', baseContext);

      await homePage.addProductToCartByQuickView(page, 1, 1);

      const result = await homePage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.name).to.equal(customCartData.firstProduct.name),
        expect(result.price).to.equal(customCartData.firstProduct.price),
        expect(result.size).to.equal('S'),
        expect(result.color).to.equal('White'),
        expect(result.quantity).to.equal(1),
        expect(result.cartProductsCount).to.equal(1),
        expect(result.cartSubtotal).to.equal(customCartData.firstProduct.price),
        expect(result.cartShipping).to.contains('Free'),
        expect(result.totalTaxIncl).to.contains(customCartData.firstProduct.price),
      ]);
    });
  });

  describe('Change quantity from quick view', async () => {
    it('should proceed to checkout and delete product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeBlockCartModal', baseContext);

      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.equal(cartPage.pageTitle);

      await cartPage.deleteProduct(page, 1);

      await cartPage.goToHomePage(page);
    });

    it('should change product quantity from quick view modal and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeQuantityByQuickView', baseContext);

      await homePage.addProductToCartByQuickView(page, 1, 2);

      const result = await homePage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.name).to.equal(customCartData.firstProduct.name),
        expect(result.price).to.equal(customCartData.firstProduct.price),
        expect(result.size).to.equal('S'),
        expect(result.color).to.equal('White'),
        expect(result.quantity).to.equal(2),
        expect(result.cartProductsCount).to.equal(2),
        expect(result.cartSubtotal).to.equal('€45.89'),
        expect(result.cartShipping).to.contains('Free'),
        expect(result.totalTaxIncl).to.contains('€45.89'),
      ]);
    });

    it('should proceed to checkout and delete product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeBlockCartModal', baseContext);

      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.equal(cartPage.pageTitle);

      await cartPage.deleteProduct(page, 1);

      await cartPage.goToHomePage(page);
    });
  });

  describe('Share links from quick view', async () => {
    it('should check share links \'Facebook, Twitter and Pinterest\' from quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShareLinks', baseContext);

      await homePage.quickViewProduct(page, 1);

      page = await homePage.goToSocialSharingLink(page, 'facebook');

      let url = await homePage.getCurrentURL(page);
      await expect(url).to.contains('facebook');

      page = await homePage.closePage(browserContext, page, 0);

      page = await homePage.goToSocialSharingLink(page, 'twitter');

      url = await homePage.getCurrentURL(page);
      await expect(url).to.contains('twitter');

      page = await homePage.closePage(browserContext, page, 0);

      page = await homePage.goToSocialSharingLink(page, 'pinterest');

      url = await homePage.getCurrentURL(page);
      await expect(url).to.contains('pinterest');

      page = await homePage.closePage(browserContext, page, 0);
    });
  });

  describe('Display product from quick view', async () => {
    it('should check product information from quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name.toUpperCase()).to.equal(firstProductData.name),
        expect(result.regularPrice).to.equal(firstProductData.regular_price),
        expect(result.price).to.equal(firstProductData.price),
        expect(result.discountPercentage).to.equal(firstProductData.discount_percentage),
        expect(result.taxShippingDeliveryLabel).to.equal(firstProductData.tax_shipping_delivery),
        expect(result.shortDescription).to.equal(firstProductData.short_description),
        expect(result.size).to.equal(firstProductData.size),
        expect(result.color).to.equal(firstProductData.color),
        expect(result.coverImage).to.contains(firstProductData.cover_image),
        expect(result.thumbImage).to.contains(firstProductData.thumb_image),
      ]);
    });
  });

  describe('Close quick view modal', async () => {
    it('should close quick view product modal and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      await expect(isQuickViewModalClosed).to.be.true;
    });
  });

  describe('Change combination from quick view modal', async () => {
    it('should change combination on popup and check it in cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCombination', baseContext);

      await homePage.quickViewProduct(page, 1);
      await homePage.changeCombinationAndAddToCart(page, combination);

      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(combination.quantity);

      await homePage.proceedToCheckout(page);

      const result = await cartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name.toUpperCase()).to.equal(firstProductData.name),
        expect(result.regularPrice).to.equal(firstProductData.regular_price),
        expect(result.price).to.equal(firstProductData.price),
        expect(result.discountPercentage).to.equal(firstProductData.discount),
        expect(result.size).to.equal(combination.size),
        expect(result.color).to.equal(combination.color),
        expect(result.image).to.contains(firstProductData.cover_image),
        expect(result.quantity).to.equal(combination.quantity),
        expect(result.totalPrice).to.equal(totalPrice),
      ]);
    });
  });

  describe('Select color on hover on product list', async () => {
    it('should select color on hover on product list and check it on product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor', baseContext);

      await cartPage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;

      await homePage.selectProductColor(page, 1, 'White');

      let pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(firstProductData.name);

      const imageFirstColor = await productPage.getProductInformation(page);

      await productPage.goToHomePage(page);

      await homePage.selectProductColor(page, 1, 'Black');

      pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(firstProductData.name);

      const imageSecondColor = await productPage.getProductInformation(page);

      await expect(imageFirstColor.coverImage).to.not.equal(imageSecondColor.coverImage);
    });
  });

  describe('Change image from quick view product modal', async () => {
    describe('Go to BO and create product with 2 images', async () => {
      before(async () => {
        page = await helper.newTab(browserContext);

        // Create products images
        await Promise.all([
          files.generateImage(`${productData.name}1.jpg`),
          files.generateImage(`${productData.name}2.jpg`),
        ]);
      });

      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to Products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );

        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should create Product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

        await boProductsPage.goToAddProductPage(page);

        const createProductMessage = await boAddProductPage.createEditBasicProduct(page, productData);
        await expect(createProductMessage).to.equal(boAddProductPage.settingUpdatedMessage);
      });

      it('should logout from BO', async function () {
        await loginCommon.logoutBO(this, page);
      });

      after(async () => {
        page = await boAddProductPage.closePage(browserContext, page, 0);

        /* Delete the generated images */
        await Promise.all([
          files.deleteFile(`${productData.name}1.jpg`),
          files.deleteFile(`${productData.name}2.jpg`),
        ]);
      });
    });

    describe('Check change product images on quick view modal', async () => {
      it('should search for the created product and quick view', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchForProductAndQuickView', baseContext);

        await homePage.searchProduct(page, productData.name);
        await searchResultsPage.quickViewProduct(page, 1);
      });

      it('should verify when we change thumb image in quick view modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

        const coverSecondImageURL = await searchResultsPage.selectThumbImage(page, 2);
        const coverFirstImageURL = await searchResultsPage.selectThumbImage(page, 1);

        await expect(coverSecondImageURL).to.not.equal(coverFirstImageURL);
      });
    });

    describe('Delete the created product', async () => {
      before(async () => {
        page = await helper.newTab(browserContext);
      });

      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to Products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );

        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should delete product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

        const testResult = await boProductsPage.deleteProduct(page, productData);
        await expect(testResult).to.equal(boProductsPage.productDeletedSuccessfulMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

        await boProductsPage.resetFilterCategory(page);
        const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });

      after(async () => {
        page = await boAddProductPage.closePage(browserContext, page, 0);
      });
    });
  });
})
;
