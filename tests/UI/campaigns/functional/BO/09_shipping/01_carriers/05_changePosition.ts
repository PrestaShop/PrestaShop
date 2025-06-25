import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCarriersPage,
  boDashboardPage,
  boLoginPage,
  boShippingPreferencesPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shipping_carriers_changePosition';

describe('BO - Shipping - Carriers : Change carrier position', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Change carrier position', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openTheShopPage', baseContext);

      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to the cart and checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);
      // Add the product to the cart
      await foClassicProductPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should login and go to address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToFO', baseContext);

      await foClassicCheckoutPage.clickOnSignIn(page);

      const isStepLoginComplete = await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      expect(isStepLoginComplete, 'Step Personal information is not complete').to.equal(true);
    });

    it('should continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check the carriers position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersPosition', baseContext);

      const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.equal([dataCarriers.clickAndCollect.name, dataCarriers.myCarrier.name]);
    });

    it('should open the back office in new tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBO', baseContext);

      page = await utilsPlaywright.newTab(browserContext);

      await boLoginPage.goTo(page, global.BO.URL);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });

    it('should connect to the backoffice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'connectBO', baseContext);

      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

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
      await boShippingPreferencesPage.closeSfToolBar(page);

      const pageTitle = await boShippingPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShippingPreferencesPage.pageTitle);
    });

    it('should set sort by \'Position\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setSortByPosition', baseContext);

      const textResult = await boShippingPreferencesPage.setCarrierSortOrderBy(page, 'Position', 'Ascending');
      expect(textResult).to.contain(boShippingPreferencesPage.successfulUpdateMessage);
    });

    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should sort by \'position\' \'asc\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sortByPosition', baseContext);

      const nonSortedTable = await boCarriersPage.getAllRowsColumnContent(page, 'a!position');

      await boCarriersPage.sortTable(page, 'a!position', 'asc');

      const sortedTable = await boCarriersPage.getAllRowsColumnContent(page, 'a!position');

      const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
      const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

      const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);
      expect(sortedTableFloat).to.deep.equal(expectedResult);
    });

    it('should change first carrier position to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeCarrierPosition', baseContext);

      // Get first row carrier name
      const firstRowCarrierName = await boCarriersPage.getTextColumn(page, 1, 'name');

      // Change position and check successful message
      const textResult = await boCarriersPage.changePosition(page, 1, 2);
      expect(textResult, 'Unable to change position').to.contains(boCarriersPage.successfulUpdateMessage);

      // Get second row carrier name and check if is equal the first row carrier name before changing position
      const secondRowCarrierName = await boCarriersPage.getTextColumn(page, 2, 'name');
      expect(secondRowCarrierName, 'Changing position was done wrongly').to.equal(firstRowCarrierName);
    });

    it('should go back to FO > Checkout page and check the carriers position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFO', baseContext);

      page = await boCarriersPage.changePage(browserContext, 0);

      await foClassicCheckoutPage.reloadPage(page);

      const carriers = await foClassicCheckoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.equal([dataCarriers.myCarrier.name, dataCarriers.clickAndCollect.name]);
    });
  });

  describe('POST-TEST : Reset carrier position', async () => {
    it('should go back to BO > Shipping page and reset the carriers position', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foClassicCheckoutPage.changePage(browserContext, 1);

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should reset second carrier position to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCarrierPosition', baseContext);

      // Get second row carrier name
      const secondRowCarrierName = await boCarriersPage.getTextColumn(page, 2, 'name');

      // Change position and check successful message
      const textResult = await boCarriersPage.changePosition(page, 2, 1);
      expect(textResult, 'Unable to change position').to.contains(boCarriersPage.successfulUpdateMessage);

      // Get first row carrier name and check if is equal the first row carrier name before changing position
      const firstRowCarrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(firstRowCarrierName, 'Changing position was done wrongly').to.equal(secondRowCarrierName);
    });

    it('should go to \'Shipping > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.shippingPreferencesLink,
      );
      await boShippingPreferencesPage.closeSfToolBar(page);

      const pageTitle = await boShippingPreferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShippingPreferencesPage.pageTitle);
    });

    it('should set sort by \'Price\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setSortByPrice', baseContext);

      const textResult = await boShippingPreferencesPage.setCarrierSortOrderBy(page, 'Price', 'Ascending');
      expect(textResult).to.contain(boShippingPreferencesPage.successfulUpdateMessage);
    });
  });
});
