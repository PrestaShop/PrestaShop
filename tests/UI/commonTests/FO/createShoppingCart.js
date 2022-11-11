require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const searchResultsPage = require('@pages/FO/searchResults');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/**
 * Function to Create a non-ordered shopping cart connected in the FO
 * @param orderData {object} Data to set when creating the order
 * @param baseContext {string} String to identify the test
 */
function createShoppingCart(orderData, baseContext = 'commonTests-createShoppingCart') {
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
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, orderData.customer);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it(`should search for the product ${orderData.product.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForProduct', baseContext);

      await homePage.searchProduct(page, orderData.product.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page, orderData.productQuantity);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(orderData.productQuantity);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFo', baseContext);

      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;

      const notificationNumber = await homePage.getCartNotificationsNumber(page);
      await expect(notificationNumber).to.be.equal(0);
    });
  });
}

module.exports = {createShoppingCart};
