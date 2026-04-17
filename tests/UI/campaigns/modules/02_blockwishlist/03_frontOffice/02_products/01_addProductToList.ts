// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {disableModule, enableModule} from '@commonTests/BO/modules/moduleManager';

import {
  type BrowserContext,
  dataCustomers,
  dataProducts,
  FakerProduct,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdModalWishlistPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyWishlistsPage,
  foHummingbirdMyWishlistsViewPage,
  foHummingbirdProductPage,
  foHummingbirdSearchResultsPage,
  type Page,
  utilsPlaywright,
  dataModules,
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

  // PRE-TEST : Create product out of stock not allowed
  createProductTest(productOutOfStockNotAllowed, `${baseContext}_preTest_0`);

  // PRE-TEST : Create product with a low stock
  createProductTest(productLowStock, `${baseContext}_preTest_1`);

  // PRE-TEST : Enable Blockwishlist
  enableModule(dataModules.blockwishlist, `${baseContext}_preTest_2`);

  describe('Add a product to a list', async () => {
    let browserContext: BrowserContext;
    let page: Page;
    let wishlistName: string;

    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 1);

      const productInformations = await foHummingbirdProductPage.getProductInformation(page);
      expect(productInformations.name).to.eq(dataProducts.demo_1.name);
    });

    it('should click on the button "Add to wishlist" and cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickAddToWishlistAndCancel', baseContext);

      await foHummingbirdProductPage.clickAddToWishlistButton(page);

      const hasModalLogin = await foHummingbirdModalWishlistPage.hasModalLogin(page);
      expect(hasModalLogin).to.equal(true);

      const isModalVisible = await foHummingbirdModalWishlistPage.clickCancelOnModalLogin(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on the button "Add to wishlist" and login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickAddToWishlistAndLogin', baseContext);

      await foHummingbirdProductPage.clickAddToWishlistButton(page);

      const hasModalLogin = await foHummingbirdModalWishlistPage.hasModalLogin(page);
      expect(hasModalLogin).to.equal(true);

      await foHummingbirdModalWishlistPage.clickLoginOnModalLogin(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogin', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount1', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists1', baseContext);

      await foHummingbirdMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foHummingbirdMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyWishlistsPage.pageTitle);

      wishlistName = await foHummingbirdMyWishlistsPage.getWishlistName(page, 1);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist1', baseContext);

      await foHummingbirdMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foHummingbirdMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist1', baseContext);

      const numProducts = await foHummingbirdMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(0);
    });

    it(`should search the product ${dataProducts.demo_3.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo3', baseContext);

      await foHummingbirdMyWishlistsViewPage.searchProduct(page, dataProducts.demo_3.name);
      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataProducts.demo_3.name);

      await foHummingbirdProductPage.setQuantityByArrowUpDown(page, 5, 'increment');
    });

    it('should add to the wishlist and select the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist1', baseContext);

      await foHummingbirdProductPage.clickAddToWishlistButton(page);

      const textResult = await foHummingbirdModalWishlistPage.addWishlist(page, 1);
      expect(textResult).to.equal(foHummingbirdModalWishlistPage.messageAddedToWishlist);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount2', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists2', baseContext);

      await foHummingbirdMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foHummingbirdMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyWishlistsPage.pageTitle);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist2', baseContext);

      await foHummingbirdMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foHummingbirdMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist2', baseContext);

      const numProducts = await foHummingbirdMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(1);

      const nameProduct = await foHummingbirdMyWishlistsViewPage.getProductName(page, 1);
      expect(nameProduct).to.equal(dataProducts.demo_3.name);

      const qtyProduct = await foHummingbirdMyWishlistsViewPage.getProductQuantity(page, 1);
      expect(qtyProduct).to.equal(5);

      const sizeProduct = await foHummingbirdMyWishlistsViewPage.getProductAttribute(page, 1, 'Size');
      expect(sizeProduct).to.equal('S');
    });

    it(`should search the product ${productOutOfStockNotAllowed.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductOutOfStockNotAllowed', baseContext);

      await foHummingbirdMyWishlistsViewPage.searchProduct(page, productOutOfStockNotAllowed.name);
      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(productOutOfStockNotAllowed.name);
    });

    it('should add to the wishlist and select the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist2', baseContext);

      await foHummingbirdProductPage.clickAddToWishlistButton(page);

      const textResult = await foHummingbirdModalWishlistPage.addWishlist(page, 1);
      expect(textResult).to.equal(foHummingbirdModalWishlistPage.messageAddedToWishlist);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount3', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists3', baseContext);

      await foHummingbirdMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foHummingbirdMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyWishlistsPage.pageTitle);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist3', baseContext);

      await foHummingbirdMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foHummingbirdMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist3', baseContext);

      const numProducts = await foHummingbirdMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(2);

      const nameProduct = await foHummingbirdMyWishlistsViewPage.getProductName(page, 2);
      expect(nameProduct).to.equal(productOutOfStockNotAllowed.name);

      const qtyProduct = await foHummingbirdMyWishlistsViewPage.getProductQuantity(page, 2);
      expect(qtyProduct).to.equal(1);

      const isProductOutOfStock = await foHummingbirdMyWishlistsViewPage.isProductOutOfStock(page, 2);
      expect(isProductOutOfStock).to.equal(true);

      const hasButtonAddToCartDisabled = await foHummingbirdMyWishlistsViewPage.hasButtonAddToCartDisabled(page, 2);
      expect(hasButtonAddToCartDisabled).to.equal(true);
    });

    it(`should search the product ${productLowStock.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductLowStock', baseContext);

      await foHummingbirdMyWishlistsViewPage.searchProduct(page, productLowStock.name);
      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(productLowStock.name);
    });

    it('should add to the wishlist and select the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist3', baseContext);

      await foHummingbirdProductPage.clickAddToWishlistButton(page);

      const textResult = await foHummingbirdModalWishlistPage.addWishlist(page, 1);
      expect(textResult).to.equal(foHummingbirdModalWishlistPage.messageAddedToWishlist);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount4', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists4', baseContext);

      await foHummingbirdMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foHummingbirdMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyWishlistsPage.pageTitle);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist4', baseContext);

      await foHummingbirdMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foHummingbirdMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist4', baseContext);

      const numProducts = await foHummingbirdMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(3);

      const nameProduct = await foHummingbirdMyWishlistsViewPage.getProductName(page, 3);
      expect(nameProduct).to.equal(productLowStock.name);

      const qtyProduct = await foHummingbirdMyWishlistsViewPage.getProductQuantity(page, 2);
      expect(qtyProduct).to.equal(1);

      const isProductLastItemsInStock = await foHummingbirdMyWishlistsViewPage.isProductLastItemsInStock(page, 3);
      expect(isProductLastItemsInStock).to.equal(true);
    });

    it(`should search the product ${dataProducts.demo_1.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductDemo1', baseContext);

      await foHummingbirdMyWishlistsViewPage.searchProduct(page, dataProducts.demo_1.name);
      await foHummingbirdSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataProducts.demo_1.name);
    });

    it('should select the size \'M\' and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSize', baseContext);

      await foHummingbirdProductPage.selectAttributes(page, 'select', [{name: 'size', value: 'M'}]);

      const selectedAttributeSize = await foHummingbirdProductPage.getSelectedAttribute(page, 1, 'select');
      expect(selectedAttributeSize).to.equal('M');
    });

    it('should select the color "Black" and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectColor', baseContext);

      await foHummingbirdProductPage.selectAttributes(page, 'radio', [{name: 'Color', value: 'Black'}], 2);

      const selectedAttributeColor = await foHummingbirdProductPage.getSelectedAttribute(page, 2, 'radio');
      expect(selectedAttributeColor).to.equal('Color - Black');
    });

    it('should add to the wishlist and select the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addToWishlist4', baseContext);

      await foHummingbirdProductPage.clickAddToWishlistButton(page);

      const textResult = await foHummingbirdModalWishlistPage.addWishlist(page, 1);
      expect(textResult).to.equal(foHummingbirdModalWishlistPage.messageAddedToWishlist);
    });

    it('should go to "My Account" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccount5', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to "My wishlists" page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyWishlists5', baseContext);

      await foHummingbirdMyAccountPage.goToMyWishlistsPage(page);

      const pageTitle = await foHummingbirdMyWishlistsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foHummingbirdMyWishlistsPage.pageTitle);
    });

    it('should click on the first wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickFirstWishlist5', baseContext);

      await foHummingbirdMyWishlistsPage.goToWishlistPage(page, 1);

      const pageTitle = await foHummingbirdMyWishlistsViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(wishlistName);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36496
    it('should check the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWishlist5', baseContext);

      const numProducts = await foHummingbirdMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(4);

      // const nameProduct = await foHummingbirdMyWishlistsViewPage.getProductName(page, 4);
      const nameProduct = await foHummingbirdMyWishlistsViewPage.getProductName(page, 2);
      expect(nameProduct).to.equal(dataProducts.demo_1.name);

      //const qtyProduct = await foHummingbirdMyWishlistsViewPage.getProductQuantity(page, 4);
      const qtyProduct = await foHummingbirdMyWishlistsViewPage.getProductQuantity(page, 2);
      expect(qtyProduct).to.equal(1);

      //const sizeProduct = await foHummingbirdMyWishlistsViewPage.getProductAttribute(page, 4, 'Size');
      //const sizeProduct = await foHummingbirdMyWishlistsViewPage.getProductAttribute(page, 2, 'Size');
      //expect(sizeProduct).to.equal('M');

      //const colorProduct = await foHummingbirdMyWishlistsViewPage.getProductAttribute(page, 4, 'Color');
      //const colorProduct = await foHummingbirdMyWishlistsViewPage.getProductAttribute(page, 2, 'Color');
      //expect(colorProduct).to.equal('Black');
    });

    it('should empty the wishlist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'emptyWishlist', baseContext);

      for (let idxProduct = 1; idxProduct <= 4; idxProduct++) {
        const message = await foHummingbirdMyWishlistsViewPage.removeProduct(page, 1);
        expect(message).to.equal(foHummingbirdMyWishlistsViewPage.messageSuccessfullyRemoved);
      }

      const numProducts = await foHummingbirdMyWishlistsViewPage.countProducts(page);
      expect(numProducts).to.equal(0);
    });
  });

  deleteProductTest(productOutOfStockNotAllowed, `${baseContext}_postTest_0`);

  deleteProductTest(productLowStock, `${baseContext}_postTest_1`);

  // POST-TEST : Disable Blockwishlist
  disableModule(dataModules.blockwishlist, `${baseContext}_postTest_2`);
});
