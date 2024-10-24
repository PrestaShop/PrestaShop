// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  FakerOrder,
  foClassicCartPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

/**
 * Function to Create a non-ordered shopping cart connected in the FO
 * @param orderData {FakerOrder} Data to set when creating the order
 * @param baseContext {string} String to identify the test
 */
function createShoppingCart(orderData: FakerOrder, baseContext: string = 'commonTests-createShoppingCart'): void {
  let browserContext: BrowserContext;
  let page: Page;

  describe('PRE-TEST: Create a non-ordered shopping cart being connected in the FO', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, orderData.customer);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it(`should search for the product ${orderData.products[0].product.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForProduct', baseContext);

      await foClassicHomePage.searchProduct(page, orderData.products[0].product.name);

      const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
    });

    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicSearchResultsPage.goToProductPage(page, 1);
      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page, orderData.products[0].quantity);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(orderData.products[0].quantity);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFo', baseContext);

      await foClassicHomePage.logout(page);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);

      const notificationNumber = await foClassicHomePage.getCartNotificationsNumber(page);
      expect(notificationNumber).to.be.equal(0);
    });
  });
}

export default createShoppingCart;
