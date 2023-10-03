// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {cartPage} from '@pages/FO/cart';
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import productPage from '@pages/FO/product';
import {searchResultsPage} from '@pages/FO/searchResults';

import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

/**
 * Function to Create a non-ordered shopping cart connected in the FO
 * @param orderData {OrderData} Data to set when creating the order
 * @param baseContext {string} String to identify the test
 */
function createShoppingCart(orderData: OrderData, baseContext: string = 'commonTests-createShoppingCart'): void {
  let browserContext: BrowserContext;
  let page: Page;

  describe('PRE-TEST: Create a non-ordered shopping cart being connected in the FO', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, orderData.customer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it(`should search for the product '${orderData.products[0].product.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForProduct', baseContext);

      await homePage.searchProduct(page, orderData.products[0].product.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await searchResultsPage.goToProductPage(page, 1);
      // Add the product to the cart
      await productPage.addProductToTheCart(page, orderData.products[0].quantity);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFo', baseContext);

      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);

      const notificationNumber = await homePage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });
  });
}

export default createShoppingCart;
