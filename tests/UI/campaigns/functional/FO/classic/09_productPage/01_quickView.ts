// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// BO
import boDashboardPage from '@pages/BO/dashboard';
import boProductsPage from '@pages/BO/catalog/products';
import boAddProductPage from '@pages/BO/catalog/products/add';
// FO
import {homePage} from '@pages/FO/classic/home';
import {cartPage} from '@pages/FO/classic/cart';
import {foProductPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

// Import data
import Products from '@data/demo/products';
import ProductData from '@data/faker/product';
import CartProductDetails from '@data/types/cart';
import type {ProductAttribute, ProductImageUrls} from '@data/types/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_productPage_quickView';

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

describe('FO - product page : Product quick view', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const attributes: ProductAttribute[] = [
    {
      name: 'size',
      value: 'M',
    },
    {
      name: 'color',
      value: 'Black',
    },
  ];
  const attributesQty = 4;
  const attributesTotalTaxInc = 91.78;
  const productData: ProductData = new ProductData({
    type: 'standard',
    productHasCombinations: false,
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
  });
  const firstCheckProductDetails: CartProductDetails = {
    name: Products.demo_1.name,
    price: Products.demo_1.finalPrice,
    cartSubtotal: Products.demo_1.finalPrice,
    totalTaxIncl: Products.demo_1.finalPrice,
    quantity: 1,
    cartProductsCount: 1,
    cartShipping: 'Free',
  };
  const firstCheckProductDetailsProducts: ProductAttribute[] = [
    {
      name: 'size',
      value: 'S',
    },
    {
      name: 'color',
      value: 'White',
    },
  ];
  const secondCheckProductDetails: CartProductDetails = {
    name: Products.demo_1.name,
    price: Products.demo_1.finalPrice,
    cartSubtotal: 45.89,
    totalTaxIncl: 45.89,
    quantity: 2,
    cartProductsCount: 2,
    cartShipping: 'Free',
  };
  const secondCheckProductDetailsProducts: ProductAttribute[] = [
    {
      name: 'size',
      value: 'S',
    },
    {
      name: 'color',
      value: 'White',
    },
  ];

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
    expect(isHomePage).to.eq(true);
  });

  // 1 - Add to cart from quick view
  describe('Add to cart from quick view', async () => {
    it('should add product to cart by quick view and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToCartByQuickView', baseContext);

      await homePage.addProductToCartByQuickView(page, 1, 1);

      const result = await homePage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.name).to.equal(firstCheckProductDetails.name),
        expect(result.price).to.equal(firstCheckProductDetails.price),
        expect(result.quantity).to.equal(firstCheckProductDetails.quantity),
        expect(result.cartProductsCount).to.equal(firstCheckProductDetails.cartProductsCount),
        expect(result.cartSubtotal).to.equal(firstCheckProductDetails.price),
        expect(result.cartShipping).to.contains(firstCheckProductDetails.cartShipping),
        expect(result.totalTaxIncl).to.equal(firstCheckProductDetails.totalTaxIncl),
      ]);

      const productAttributesFromBlockCart = await homePage.getProductAttributesFromBlockCartModal(page);
      await Promise.all([
        expect(productAttributesFromBlockCart.length).to.equal(2),
        expect(productAttributesFromBlockCart[0].name).to.equal(firstCheckProductDetailsProducts[0].name),
        expect(productAttributesFromBlockCart[0].value).to.equal(firstCheckProductDetailsProducts[0].value),
        expect(productAttributesFromBlockCart[1].name).to.equal(firstCheckProductDetailsProducts[1].name),
        expect(productAttributesFromBlockCart[1].value).to.equal(firstCheckProductDetailsProducts[1].value),
      ]);
    });
  });

  // 2 - Change quantity from quick view
  describe('Change quantity from quick view', async () => {
    it('should proceed to checkout and delete product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductFromCart1', baseContext);

      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);

      await cartPage.deleteProduct(page, 1);

      await cartPage.goToHomePage(page);
    });

    it('should change product quantity from quick view modal and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeQuantityByQuickView', baseContext);

      await homePage.addProductToCartByQuickView(page, 1, 2);

      const result = await homePage.getProductDetailsFromBlockCartModal(page);
      await Promise.all([
        expect(result.name).to.equal(secondCheckProductDetails.name),
        expect(result.price).to.equal(secondCheckProductDetails.price),
        expect(result.quantity).to.equal(secondCheckProductDetails.quantity),
        expect(result.cartProductsCount).to.equal(secondCheckProductDetails.cartProductsCount),
        expect(result.cartSubtotal).to.equal(secondCheckProductDetails.cartSubtotal),
        expect(result.cartShipping).to.contains(secondCheckProductDetails.cartShipping),
        expect(result.totalTaxIncl).to.equal(secondCheckProductDetails.totalTaxIncl),
      ]);

      const productAttributesFromBlockCart = await homePage.getProductAttributesFromBlockCartModal(page);
      await Promise.all([
        expect(productAttributesFromBlockCart.length).to.equal(2),
        expect(productAttributesFromBlockCart[0].name).to.equal(secondCheckProductDetailsProducts[0].name),
        expect(productAttributesFromBlockCart[0].value).to.equal(secondCheckProductDetailsProducts[0].value),
        expect(productAttributesFromBlockCart[1].name).to.equal(secondCheckProductDetailsProducts[1].name),
        expect(productAttributesFromBlockCart[1].value).to.equal(secondCheckProductDetailsProducts[1].value),
      ]);
    });

    it('should proceed to checkout and delete product from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProductFromCart2', baseContext);

      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);

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

    tests.forEach((test, index: number) => {
      it(`should check share link of '${test.args.name}' from quick view modal`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkShareLink${index}`, baseContext);

        if (index === 0) {
          await homePage.quickViewProduct(page, 1);
        }

        const url = await homePage.getSocialSharingLink(page, test.args.name);
        expect(url).to.contain(test.result.url);
      });
    });
  });

  // 4 - Check product information from quick view
  describe('Display product from quick view', async () => {
    it('should check product information from quick view modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const result = await homePage.getProductWithDiscountDetailsFromQuickViewModal(page);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_1.name),
        expect(result.regularPrice).to.equal(Products.demo_1.retailPrice),
        expect(result.price).to.equal(Products.demo_1.finalPrice),
        expect(result.discountPercentage).to.equal(`Save ${Products.demo_1.specificPrice.discount}%`),
        expect(result.taxShippingDeliveryLabel).to.equal('Tax included'),
        expect(result.shortDescription).to.equal(Products.demo_1.summary),
        expect(result.coverImage).to.contains(Products.demo_1.coverImage),
        expect(result.thumbImage).to.contains(Products.demo_1.coverImage),
      ]);

      const productAttributesFromQuickView = await homePage.getProductAttributesFromQuickViewModal(page);
      await Promise.all([
        expect(productAttributesFromQuickView.length).to.equal(2),
        expect(productAttributesFromQuickView[0].name).to.equal('size'),
        expect(productAttributesFromQuickView[0].value).to.equal('S M L XL'),
        expect(productAttributesFromQuickView[1].name).to.equal('color'),
        expect(productAttributesFromQuickView[1].value).to.equal('White Black'),
      ]);
    });
  });

  // 5 - Close quick view modal
  describe('Close quick view modal', async () => {
    it('should close quick view product modal and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeQuickOptionModal', baseContext);

      const isQuickViewModalClosed = await homePage.closeQuickViewModal(page);
      expect(isQuickViewModalClosed).to.eq(true);
    });
  });

  // 6 - Change combination from quick view
  describe('Change combination from quick view modal', async () => {
    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewProduct', baseContext);

      await homePage.quickViewProduct(page, 1);

      const isModalVisible = await homePage.isQuickViewProductModalVisible(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should change combination on popup and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCombination', baseContext);

      await homePage.changeAttributesAndAddToCart(page, attributes, attributesQty);
      await homePage.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should check the products number in the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductsNumber', baseContext);

      const notificationsNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(attributesQty);
    });

    it('should check product details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);

      const result = await cartPage.getProductDetail(page, 1);
      await Promise.all([
        expect(result.name).to.equal(Products.demo_1.name),
        expect(result.regularPrice).to.equal(Products.demo_1.retailPrice),
        expect(result.price).to.equal(Products.demo_1.finalPrice),
        expect(result.discountPercentage).to.equal(`-${Products.demo_1.specificPrice.discount}%`),
        expect(result.image).to.contains(Products.demo_1.coverImage),
        expect(result.quantity).to.equal(attributesQty),
        expect(result.totalPrice).to.equal(attributesTotalTaxInc),
      ]);

      const cartProductAttributes = await cartPage.getProductAttributes(page, 1);
      await Promise.all([
        expect(cartProductAttributes.length).to.equal(2),
        expect(cartProductAttributes[0].name).to.equal(attributes[0].name),
        expect(cartProductAttributes[0].value).to.equal(attributes[0].value),
        expect(cartProductAttributes[1].name).to.equal(attributes[1].name),
        expect(cartProductAttributes[1].value).to.equal(attributes[1].value),
      ]);
    });
  });

  // 7 - Select color on hover from product list
  describe('Select color on hover on product list', async () => {
    let imageFirstColor: ProductImageUrls;
    let imageSecondColor: ProductImageUrls;

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomeToSelectColor', baseContext);

      await cartPage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should select color \'Ẁhite\' and be on product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor1', baseContext);

      await homePage.selectProductColor(page, 1, 'White');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should get product image Url and go back to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getProductImage1', baseContext);

      imageFirstColor = await foProductPage.getProductImageUrls(page);

      await foProductPage.goToHomePage(page);
      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should select color \'Black\' and be on product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor2', baseContext);

      await homePage.selectProductColor(page, 1, 'Black');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should product image be different from the ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getProductImage2', baseContext);

      imageSecondColor = await foProductPage.getProductImageUrls(page);
      expect(imageFirstColor.coverImage).to.not.equal(imageSecondColor.coverImage);
    });
  });

  // 8 - Change image from quick view
  describe('Change image from quick view product modal', async () => {
    describe('Go to BO and create product with 2 images', async () => {
      before(async () => {
        page = await helper.newTab(browserContext);

        // Create products images
        await Promise.all([
          files.generateImage(productData.coverImage ?? ''),
          files.generateImage(productData.thumbImage ?? ''),
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
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.eq(true);
      });

      it('should choose \'Standard product\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

        await boProductsPage.selectProductType(page, productData.type);

        const pageTitle = await boAddProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(boAddProductPage.pageTitle);
      });

      it('should go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await boAddProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(boAddProductPage.pageTitle);
      });

      it('should create product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

        await boAddProductPage.closeSfToolBar(page);

        const createProductMessage = await boAddProductPage.setProduct(page, productData);
        expect(createProductMessage).to.equal(boAddProductPage.successfulUpdateMessage);
      });

      it('should logout from BO', async function () {
        await loginCommon.logoutBO(this, page);
      });

      after(async () => {
        page = await boAddProductPage.closePage(browserContext, page, 0);

        /* Delete the generated images */
        await Promise.all([
          files.deleteFile(productData.coverImage ?? ''),
          files.deleteFile(productData.thumbImage ?? ''),
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

        expect(coverSecondImageURL).to.not.equal(coverFirstImageURL);
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
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should click on delete product button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct2', baseContext);

        const isModalVisible = await boProductsPage.clickOnDeleteProductButton(page);
        expect(isModalVisible).to.be.eq(true);
      });

      it('should delete product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

        const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
        expect(textMessage).to.equal(boProductsPage.successfulDeleteMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

        const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });

      after(async () => {
        page = await boAddProductPage.closePage(browserContext, page, 0);
      });
    });
  });
});
