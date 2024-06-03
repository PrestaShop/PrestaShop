// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import carriersPage from '@pages/BO/shipping/carriers';
import preferencesPage from '@pages/BO/shipping/preferences';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import data
import {
  boDashboardPage,
  dataCarriers,
  dataCustomers,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shipping_carriers_changePosition';

describe('BO - Shipping - Carriers : Change carrier position', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Change carrier position', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openTheShopPage', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to the cart and checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      // Go to the first product page
      await homePage.goToProductPage(page, 1);
      // Add the product to the cart
      await productPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should login and go to address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isStepLoginComplete = await checkoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isStepLoginComplete, 'Step Personal information is not complete').to.equal(true);
    });

    it('should continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check the carriers position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPosition', baseContext);

      const carriers = await checkoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.equal([dataCarriers.clickAndCollect.name, dataCarriers.myCarrier.name]);
    });

    it('should open the back office in new tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCarriersFilters', baseContext);

      page = await helper.newTab(browserContext);
      await checkoutPage.goToBO(page);

      await loginCommon.loginBO(this, page);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shipping > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.shippingPreferencesLink,
      );
      await preferencesPage.closeSfToolBar(page);

      const pageTitle = await preferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    it('should set sort by \'Position\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setSortByPosition', baseContext);

      const textResult = await preferencesPage.setCarrierSortOrderBy(page, 'Position', 'Ascending');
      expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
    });

    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await carriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should change first carrier position to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCarrierPosition', baseContext);

      // Get first row carrier name
      const firstRowCarrierName = await carriersPage.getTextColumn(page, 1, 'name');

      // Change position and check successful message
      const textResult = await carriersPage.changePosition(page, 1, 2);
      expect(textResult, 'Unable to change position').to.contains(carriersPage.successfulUpdateMessage);

      // Get second row carrier name and check if is equal the first row carrier name before changing position
      const secondRowCarrierName = await carriersPage.getTextColumn(page, 2, 'name');
      expect(secondRowCarrierName, 'Changing position was done wrongly').to.equal(firstRowCarrierName);
    });

    it('should go back to FO > Checkout page and check the carriers position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO', baseContext);

      page = await carriersPage.changePage(browserContext, 0);

      await checkoutPage.reloadPage(page);

      const carriers = await checkoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.equal([dataCarriers.myCarrier.name, dataCarriers.clickAndCollect.name]);
    });
  });

  describe('POST-TEST : Reset carrier position', async () => {
    it('should go back to BO > Shipping page and reset the carriers position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await checkoutPage.changePage(browserContext, 1);

      const pageTitle = await carriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    it('should reset second carrier position to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCarrierPosition', baseContext);

      // Get second row carrier name
      const secondRowCarrierName = await carriersPage.getTextColumn(page, 2, 'name');

      // Change position and check successful message
      const textResult = await carriersPage.changePosition(page, 2, 1);
      expect(textResult, 'Unable to change position').to.contains(carriersPage.successfulUpdateMessage);

      // Get first row carrier name and check if is equal the first row carrier name before changing position
      const firstRowCarrierName = await carriersPage.getTextColumn(page, 1, 'name');
      expect(firstRowCarrierName, 'Changing position was done wrongly').to.equal(secondRowCarrierName);
    });

    it('should go to \'Shipping > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.shippingPreferencesLink,
      );
      await preferencesPage.closeSfToolBar(page);

      const pageTitle = await preferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    it('should set sort by \'Price\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setSortByPrice', baseContext);

      const textResult = await preferencesPage.setCarrierSortOrderBy(page, 'Price', 'Ascending');
      expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
    });
  });
});
