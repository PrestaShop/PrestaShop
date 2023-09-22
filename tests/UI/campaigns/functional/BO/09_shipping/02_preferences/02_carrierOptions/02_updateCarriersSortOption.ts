// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import carriersPage from '@pages/BO/shipping/carriers';
import preferencesPage from '@pages/BO/shipping/preferences';
// Import FO pages
import {cartPage} from '@pages/FO/cart';
import foCheckoutPage from '@pages/FO/checkout';
import {homePage as foHomePage} from '@pages/FO/home';
import foProductPage from '@pages/FO/product';

// Import data
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shipping_preferences_carrierOptions_updateCarriersSortOption';

/*
Go to shipping > Carriers page
Activate the 2 carriers 'My cheap carrier' and 'My light carrier'
Go to shipping > preferences page
Choose sort by 'Price' and orderBy 'Ascending/Descending'
Go to FO and check the carriers sort
Go back to BO and choose sort by 'Position' and orderBy 'Ascending/Descending'
Go to FO and check the carriers sort
Go back to BO > shipping > Carriers
Disable the 2 carriers 'My cheap carrier' and 'My light carrier'
 */
describe('BO - Shipping - Preferences : Update \'sort carriers by\' and \'Order carriers by\'', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCarriers: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPageToEnable', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.carriersLink,
    );

    const pageTitle = await carriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(carriersPage.pageTitle);
  });

  const carriersNames: string[] = [
    Carriers.cheapCarrier.name,
    Carriers.lightCarrier.name,
  ];

  describe(`Enable the 2 carriers '${Carriers.cheapCarrier.name}' and '${Carriers.lightCarrier.name}'`, async () => {
    it('should reset all filters and get number of carriers in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCarriers = await carriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriers).to.be.above(0);
    });

    carriersNames.forEach((carrierName: string, index: number) => {
      it(`should filter list by name ${carrierName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByName${index}`, baseContext);

        await carriersPage.filterTable(page, 'input', 'name', carrierName);

        const textColumn = await carriersPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(carrierName);
      });

      it('should enable the carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `enableCarrier${index}`, baseContext);

        const isActionPerformed = await carriersPage.setStatus(page, 1, true);

        if (isActionPerformed) {
          const resultMessage = await carriersPage.getAlertSuccessBlockContent(page);
          expect(resultMessage).to.contains(carriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await carriersPage.getStatus(page, 1);
        expect(carrierStatus).to.eq(true);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterEnable${index}`, baseContext);

        const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
      });
    });
  });

  describe('Choose the sort option in BO and check it in FO', async () => {
    it('should go to \'Shipping > Preferences\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPreferencesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shippingLink,
        dashboardPage.shippingPreferencesLink,
      );
      await preferencesPage.closeSfToolBar(page);

      const pageTitle = await preferencesPage.getPageTitle(page);
      expect(pageTitle).to.contains(preferencesPage.pageTitle);
    });

    const sortByPosition: string[] = [
      Carriers.default.name,
      Carriers.myCarrier.name,
      Carriers.cheapCarrier.name,
      Carriers.lightCarrier.name,
    ];
    [
      {args: {sortBy: 'Position', orderBy: 'Ascending'}},
      {args: {sortBy: 'Position', orderBy: 'Descending'}},
      {args: {sortBy: 'Price', orderBy: 'Descending'}},
      {args: {sortBy: 'Price', orderBy: 'Ascending'}},
    ].forEach((test, index: number) => {
      it(`should set sort by '${test.args.sortBy}' and order by '${test.args.orderBy}' in BO`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCarrier${index}`, baseContext);

        const textResult = await preferencesPage.setCarrierSortOrderBy(page, test.args.sortBy, test.args.orderBy);
        expect(textResult).to.contain(preferencesPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Click on view my shop
        page = await preferencesPage.viewMyShop(page);
        // Change FO language
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to shipping step in checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkFinalSummary${index}`, baseContext);

        // Go to the first product page
        await foHomePage.goToProductPage(page, 1);
        // Add the product to the cart
        await foProductPage.addProductToTheCart(page);
        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);
        // Checkout the order
        if (index === 0) {
          // Personal information step - Login
          await foCheckoutPage.clickOnSignIn(page);
          await foCheckoutPage.customerLogin(page, Customers.johnDoe);
        }

        // Address step - Go to delivery step
        const isStepAddressComplete = await foCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should verify the sort of carriers', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSort${index}`, baseContext);

        if (test.args.sortBy === 'Price') {
          const sortedCarriers = await foCheckoutPage.getAllCarriersPrices(page);
          const expectedResult = await basicHelper.sortArray(sortedCarriers);

          if (test.args.orderBy === 'Ascending') {
            expect(sortedCarriers).to.deep.equal(expectedResult);
          } else {
            expect(sortedCarriers).to.deep.equal(expectedResult.reverse());
          }
        } else if (test.args.sortBy === 'Position') {
          const sortedCarriers = await foCheckoutPage.getAllCarriersNames(page);

          if (test.args.orderBy === 'Ascending') {
            expect(sortedCarriers).to.deep.equal(sortByPosition);
          } else {
            expect(sortedCarriers).to.deep.equal(sortByPosition.reverse());
          }
        }
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        page = await foCheckoutPage.closePage(browserContext, page, 0);

        const pageTitle = await preferencesPage.getPageTitle(page);
        expect(pageTitle).to.contains(preferencesPage.pageTitle);
      });
    });
  });

  describe(`Disable the 2 carriers '${Carriers.cheapCarrier.name}' and '${Carriers.lightCarrier.name}'`, async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPageToDisable', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shippingLink,
        dashboardPage.carriersLink,
      );

      const pageTitle = await carriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(carriersPage.pageTitle);
    });

    carriersNames.forEach((carrierName: string, index: number) => {
      it(`should filter list by name ${carrierName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByName${index}ToDisable`, baseContext);

        await carriersPage.filterTable(page, 'input', 'name', carrierName);

        const textColumn = await carriersPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(carrierName);
      });

      it('should disable the carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `disableCarrier${index}`, baseContext);

        const isActionPerformed = await carriersPage.setStatus(page, 1, false);

        if (isActionPerformed) {
          const resultMessage = await carriersPage.getAlertSuccessBlockContent(page);
          expect(resultMessage).to.contains(carriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await carriersPage.getStatus(page, 1);
        expect(carrierStatus).to.eq(false);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDisable${index}`, baseContext);

        const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
      });
    });
  });
});
