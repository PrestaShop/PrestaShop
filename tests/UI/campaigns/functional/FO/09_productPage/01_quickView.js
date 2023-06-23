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
const combination = {
  size: 'M',
  color: 'Black',
  quantity: 4,
  totalPrice: 91.78,
};
const productToCreate = {
  type: 'Standard product',
  productHasCombinations: false,
  coverImage: 'cover.jpg',
  thumbImage: 'thumb.jpg',
};
const productData = new ProductFaker(productToCreate);
const firstCheckProductDetails = {
  name: customCartData.firstProduct.name,
  price: customCartData.firstProduct.price,
  size: 'S',
  color: 'White',
  quantity: 1,
  shipping: 'Free',
};
const secondCheckProductDetails = {
  name: customCartData.firstProduct.name,
  price: customCartData.firstProduct.price,
  size: 'S',
  color: 'White',
  quantity: 2,
  subtotal: 45.89,
  shipping: 'Free',
  totalTaxInc: 45.89,
};

let imageFirstColor = {};

/*
Add to cart from quick view
Change quantity from quick view
Share links from quick view
Check product information from quick view
Close quick view modal
Change combination from quick view
Select color on hover from product list
Change image from quick view
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

  // 1 - Add to cart from quick view
  describe('Add to cart from quick view', async () => {
    it('should add product to cart by quick view and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToCartByQuickView', baseContext);

      await homePage.addProductToCartByQuickView(page, 1, 1);

      let result = await homePage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.name).to.equal(firstCheckProductDetails.name),
        expect(result.price).to.equal(firstCheckProductDetails.price),
        expect(result.quantity).to.equal(firstCheckProductDetails.quantity),
        expect(result.cartProductsCount).to.equal(firstCheckProductDetails.quantity),
        expect(result.cartSubtotal).to.equal(firstCheckProductDetails.price),
        expect(result.cartShipping).to.contains(firstCheckProductDetails.shipping),
        expect(result.totalTaxIncl).to.equal(firstCheckProductDetails.price),
      ]);

      result = await homePage.getProductAttributesFromBlockCartModal(page);
      await Promise.all([
        expect(result.size).to.equal(firstCheckProductDetails.size),
        expect(result.color).to.equal(firstCheckProductDetails.color),
      ]);
    });
  });

  // 2 - Change quantity from quick view
  describe('Change quantity from quick view', async () => {
    it('should proceed to checkout and delete product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductFromCart1', baseContext);

      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.equal(cartPage.pageTitle);

      await cartPage.deleteProduct(page, 1);

      await cartPage.goToHomePage(page);
    });

    it('should change product quantity from quick view modal and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeQuantityByQuickView', baseContext);

      await homePage.addProductToCartByQuickView(page, 1, 2);

      let result = await homePage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.name).to.equal(secondCheckProductDetails.name),
        expect(result.price).to.equal(secondCheckProductDetails.price),
        expect(result.quantity).to.equal(secondCheckProductDetails.quantity),
        expect(result.cartProductsCount).to.equal(secondCheckProductDetails.quantity),
        expect(result.cartSubtotal).to.equal(secondCheckProductDetails.subtotal),
        expect(result.cartShipping).to.contains(secondCheckProductDetails.shipping),
        expect(result.totalTaxIncl).to.equal(secondCheckProductDetails.totalTaxInc),
      ]);

      result = await homePage.getProductAttributesFromBlockCartModal(page);
      await Promise.all([
        expect(result.size).to.equal(secondCheckProductDetails.size),
        expect(result.color).to.equal(secondCheckProductDetails.color),
      ]);
    });

    it('should proceed to checkout and delete product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductFromCart2', baseContext);

      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      await expect(pageTitle).to.equal(cartPage.pageTitle);

      await cartPage.deleteProduct(page, 1);

      await cartPage.goToHomePage(page);
    });
  });

  // 3 - Share links from quick view
  describe('Share links from quick view', async () => {
    const tests = [
      {
        args:
          {
            name: 'Facebook',
          },
        result:
          {
            url: 'https://www.facebook.com/',
          },
      },
      {
        args:
          {
            name: 'Twitter',
          },
        result:
          {
            url: 'https://twitter.com/',
          },
      },
      {
        args:
          {
            name: 'Pinterest',
          },
        result:
          {
            url: 'https://www.pinterest.com/',
          },
      },
    ];

    tests.forEach((test, index) => {
      it(`should check share link of '${test.args.name}' from quick view modal`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkShareLink${index}`, baseContext);

        if (index === 0) {
          await homePage.quickViewProduct(page, 1);
        }

        const url = await homePage.getSocialSharingLink(page, test.args.name);
        await expect(url).to.contain(test.result.url);
      });
    });
  });

  // 4 - Check product information from quick view
  describe('Display product from quick view', async () => {
    it('should check product information from quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      let result = await homePage.getProductDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name.toUpperCase()).to.equal(firstProductData.name),
        expect(result.regularPrice).to.equal(firstProductData.regular_price),
        expect(result.price).to.equal(firstProductData.price),
        expect(result.discountPercentage).to.equal(firstProductData.discount_percentage),
        expect(result.taxShippingDeliveryLabel).to.equal(firstProductData.tax_shipping_delivery),
        expect(result.shortDescription).to.equal(firstProductData.short_description),
        expect(result.coverImage).to.contains(firstProductData.cover_image),
        expect(result.thumbImage).to.contains(firstProductData.thumb_image),
      ]);

      result = await homePage.getProductAttributesFromQuickViewModal(page);
      await Promise.all([
        expect(result.size).to.equal(firstProductData.size),
        expect(result.color).to.equal(firstProductData.color),
      ]);
    });
  });

  // 5 - Close quick view modal
  describe('Close quick view modal', async () => {
    it('should close quick view product modal and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      await expect(isQuickViewModalClosed).to.be.true;
    });
  });

  // 6 - Change combination from quick view
  describe('Change combination from quick view modal', async () => {
    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct', baseContext);

      await homePage.quickViewProduct(page, 1);

      const isModalVisible = await homePage.isQuickViewProductModalVisible(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should change combination on popup and check it in cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCombination', baseContext);

      await homePage.changeCombinationAndAddToCart(page, combination);

      await homePage.proceedToCheckout(page);

      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(combination.quantity);
    });

    it('should check product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);

      let result = await cartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name.toUpperCase()).to.equal(firstProductData.name),
        expect(result.regularPrice).to.equal(firstProductData.regular_price),
        expect(result.price).to.equal(firstProductData.price),
        expect(result.discountPercentage).to.equal(firstProductData.discount),
        expect(result.image).to.contains(firstProductData.cover_image),
        expect(result.quantity).to.equal(combination.quantity),
        expect(result.totalPrice).to.equal(combination.totalPrice),
      ]);

      result = await cartPage.getProductAttributes(page, 1);
      await Promise.all([
        expect(result.size).to.equal(combination.size),
        expect(result.color).to.equal(combination.color),
      ]);
    });
  });

  // 7 - Select color on hover from product list
  describe('Select color on hover on product list', async () => {
    it('should select color on hover on product list and check it on product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor', baseContext);

      await cartPage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should select color \'Ẁhite\' and be on product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor', baseContext);

      await homePage.selectProductColor(page, 1, 'White');

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(firstProductData.name);
    });

    it('should get product image Url and go back to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor', baseContext);

      imageFirstColor = await productPage.getProductImageUrls(page);
      await productPage.goToHomePage(page);
    });

    it('should select color \'Black\' and be on product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor2', baseContext);

      await homePage.selectProductColor(page, 1, 'Black');

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle.toUpperCase()).to.contains(firstProductData.name);
    });

    it('should product image be different from the ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getProductImage2', baseContext);

      const imageSecondColor = await productPage.getProductImageUrls(page);
      await expect(imageFirstColor.coverImage).to.not.equal(imageSecondColor.coverImage);
    });
  });

  // 8 - Change image from quick view
  describe('Change image from quick view product modal', async () => {
    describe('Go to BO and create product with 2 images', async () => {
      before(async () => {
        page = await helper.newTab(browserContext);

        // Create products images
        await Promise.all([
          files.generateImage(productData.coverImage),
          files.generateImage(productData.thumbImage),
        ]);
      });

      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to Products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToCreate', baseContext);

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
          files.deleteFile(productData.coverImage),
          files.deleteFile(productData.thumbImage),
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
        await testContext.addContextItem(this, 'testIdentifier', 'verifyThumbImage', baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

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

        await boProductsPage.resetFilter(page);
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
});
