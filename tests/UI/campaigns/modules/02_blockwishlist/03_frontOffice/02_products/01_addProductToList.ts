// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

import {
  type BrowserContext,
  dataCustomers,
  dataProducts,
  FakerProduct,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalWishlistPage,
  foClassicMyAccountPage,
  foClassicMyWishlistsPage,
  foClassicMyWishlistsViewPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'modules_blockwishlist_frontOffice_products_addProductToList';

describe('Wishlist module - Add a product to a list', async () => {
  const productOutOfStockNotAllowed: FakerProduct = new FakerProduct({
    name: 'Product Out of stock not allowed',
    type: 'standard',
    taxRule: 'No tax',
    tax: 0,
    quantity: 0,
    behaviourOutOfStock: 'Deny orders',
  });
  const productLowStock: FakerProduct = new FakerProduct({
    name: 'Product Low Stock',
    type: 'standard',
    taxRule: 'No tax',
    tax: 0,
    quantity: 2,
  });

  let browserContext: BrowserContext;
  let page: Page;
  let wishlistName: string;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productOutOfStockNotAllowed, `${baseContext}_preTest_0`);

  // Pre-condition : Create product with a low stock
  createProductTest(productLowStock, `${baseContext}_preTest_1`);

  describe('Add a product to a list', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const productInformations = await foClassicProductPage.getProductInformation(page);
      expect(productInformations.name).to.eq(dataProducts.demo_1.name);
    });

    it('should click on the button "Add to wishlist" and cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickAddToWishlistAndCancel', baseContext);

      await foClassicProductPage.clickAddToWishlistButton(page);

      const hasModalLogin = await foClassicModalWishlistPage.hasModalLogin(page);
      expect(hasModalLogin).to.equal(true);

      const isModalVisible = await foClassicModalWishlistPage.clickCancelOnModalLogin(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on the button "Add to wishlist" and login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickAddToWishlistAndLogin', baseContext);

      await foClassicProductPage.clickAddToWishlistButton(page);

      const hasModalLogin = await foClassicModalWishlistPage.hasModalLogin(page);
      expect(hasModalLogin).to.equal(true);

      await foClassicModalWishlistPage.clickLoginOnModalLogin(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogin', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount1', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists1', baseContext);

      await foClassicMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyWishlistsPage.pageTitle);

      wishlistName = await foClassicMyWishlistsPage.getWishlistName(page, 1);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist1', baseContext);

      await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist1', baseContext);

      const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(0);
    });

    it(`should search the product ${dataProducts.demo_3.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo3', baseContext);

      await foClassicMyWishlistsViewPage.searchProduct(page, dataProducts.demo_3.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataProducts.demo_3.name);

      await foClassicProductPage.setQuantityByArrowUpDown(page, 5, 'up');
    });

    it('should add to the wishlist and select the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist1', baseContext);

      await foClassicProductPage.clickAddToWishlistButton(page);

      const textResult = await foClassicModalWishlistPage.addWishlist(page, 1);
      expect(textResult).to.equal(foClassicModalWishlistPage.messageAddedToWishlist);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount2', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists2', baseContext);

      await foClassicMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyWishlistsPage.pageTitle);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist2', baseContext);

      await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist2', baseContext);

      const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(1);

      const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 1);
      expect(nameProduct).to.equal(dataProducts.demo_3.name);

      const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 1);
      expect(qtyProduct).to.equal(5);

      const sizeProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 1, 'Size');
      expect(sizeProduct).to.equal('S');
    });

    it(`should search the product ${productOutOfStockNotAllowed.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductOutOfStockNotAllowed', baseContext);

      await foClassicMyWishlistsViewPage.searchProduct(page, productOutOfStockNotAllowed.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(productOutOfStockNotAllowed.name);
    });

    it('should add to the wishlist and select the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist2', baseContext);

      await foClassicProductPage.clickAddToWishlistButton(page);

      const textResult = await foClassicModalWishlistPage.addWishlist(page, 1);
      expect(textResult).to.equal(foClassicModalWishlistPage.messageAddedToWishlist);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount3', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists3', baseContext);

      await foClassicMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyWishlistsPage.pageTitle);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist3', baseContext);

      await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist3', baseContext);

      const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(2);

      const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 2);
      expect(nameProduct).to.equal(productOutOfStockNotAllowed.name);

      const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 2);
      expect(qtyProduct).to.equal(1);

      const isProductOutOfStock = await foClassicMyWishlistsViewPage.isProductOutOfStock(page, 2);
      expect(isProductOutOfStock).to.equal(true);

      const hasButtonAddToCartDisabled = await foClassicMyWishlistsViewPage.hasButtonAddToCartDisabled(page, 2);
      expect(hasButtonAddToCartDisabled).to.equal(true);
    });

    it(`should search the product ${productLowStock.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductLowStock', baseContext);

      await foClassicMyWishlistsViewPage.searchProduct(page, productLowStock.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(productLowStock.name);
    });

    it('should add to the wishlist and select the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist3', baseContext);

      await foClassicProductPage.clickAddToWishlistButton(page);

      const textResult = await foClassicModalWishlistPage.addWishlist(page, 1);
      expect(textResult).to.equal(foClassicModalWishlistPage.messageAddedToWishlist);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount4', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists4', baseContext);

      await foClassicMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyWishlistsPage.pageTitle);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist4', baseContext);

      await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist4', baseContext);

      const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(3);

      const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 3);
      expect(nameProduct).to.equal(productLowStock.name);

      const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 2);
      expect(qtyProduct).to.equal(1);

      const isProductLastItemsInStock = await foClassicMyWishlistsViewPage.isProductLastItemsInStock(page, 3);
      expect(isProductLastItemsInStock).to.equal(true);
    });

    it(`should search the product ${dataProducts.demo_1.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo1', baseContext);

      await foClassicMyWishlistsViewPage.searchProduct(page, dataProducts.demo_1.name);
      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataProducts.demo_1.name);
    });

    it('should select the size \'M\' / color "Black" and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSizeColor', baseContext);

      await foClassicProductPage.selectAttributes(page, 'select', [{name: 'size', value: 'M'}]);
      await foClassicProductPage.selectAttributes(page, 'radio', [{name: 'Color', value: 'Black'}], 2);

      const selectedAttributeSize = await foClassicProductPage.getSelectedAttribute(page, 1, 'select');
      expect(selectedAttributeSize).to.equal('M');

      const selectedAttributeColor = await foClassicProductPage.getSelectedAttribute(page, 2, 'radio');
      expect(selectedAttributeColor).to.equal('Black');
    });

    it('should add to the wishlist and select the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist4', baseContext);

      await foClassicProductPage.clickAddToWishlistButton(page);

      const textResult = await foClassicModalWishlistPage.addWishlist(page, 1);
      expect(textResult).to.equal(foClassicModalWishlistPage.messageAddedToWishlist);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount5', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists5', baseContext);

      await foClassicMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foClassicMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyWishlistsPage.pageTitle);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist5', baseContext);

      await foClassicMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foClassicMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36496
    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist5', baseContext);

      const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(4);

      // const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 4);
      const nameProduct = await foClassicMyWishlistsViewPage.getProductName(page, 2);
      expect(nameProduct).to.equal(dataProducts.demo_1.name);

      //const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 4);
      const qtyProduct = await foClassicMyWishlistsViewPage.getProductQuantity(page, 2);
      expect(qtyProduct).to.equal(1);

      //const sizeProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 4, 'Size');
      //const sizeProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 2, 'Size');
      //expect(sizeProduct).to.equal('M');

      //const colorProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 4, 'Color');
      //const colorProduct = await foClassicMyWishlistsViewPage.getProductAttribute(page, 2, 'Color');
      //expect(colorProduct).to.equal('Black');
    });

    it('should empty the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'emptyWishlist', baseContext);

      for (let idxProduct = 1; idxProduct <= 4; idxProduct++) {
        const message = await foClassicMyWishlistsViewPage.removeProduct(page, 1);
        expect(message).to.equal(foClassicMyWishlistsViewPage.messageSuccessfullyRemoved);
      }

      const numProducts = await foClassicMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(0);
    });
  });

  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest_0`);

  deleteProductTest(productLowStock, `${baseContext}_postTest_1`);
});
