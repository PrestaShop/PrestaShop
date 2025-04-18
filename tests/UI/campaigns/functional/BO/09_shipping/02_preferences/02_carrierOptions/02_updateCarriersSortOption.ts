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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPageToEnable', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);
  });

  const carriersNames: string[] = [
    dataCarriers.myCheapCarrier.name,
    dataCarriers.myLightCarrier.name,
  ];

  describe(`Enable the 2 carriers '${dataCarriers.myCheapCarrier.name}' and '${dataCarriers.myLightCarrier.name}'`, async () => {
    it('should reset all filters and get number of carriers in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCarriers = await boCarriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriers).to.be.above(0);
    });

    carriersNames.forEach((carrierName: string, index: number) => {
      it(`should filter list by name ${carrierName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByName${index}`, baseContext);

        await boCarriersPage.filterTable(page, 'input', 'name', carrierName);

        const textColumn = await boCarriersPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(carrierName);
      });

      it('should enable the carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `enableCarrier${index}`, baseContext);

        const isActionPerformed = await boCarriersPage.setStatus(page, 1, true);

        if (isActionPerformed) {
          const resultMessage = await boCarriersPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(boCarriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await boCarriersPage.getStatus(page, 1);
        expect(carrierStatus).to.eq(true);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterEnable${index}`, baseContext);

        const numberOfCarriersAfterReset = await boCarriersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
      });
    });
  });

  describe('Choose the sort option in BO and check it in FO', async () => {
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

    const sortByPosition: string[] = [
      dataCarriers.clickAndCollect.name,
      dataCarriers.myCarrier.name,
      dataCarriers.myCheapCarrier.name,
      dataCarriers.myLightCarrier.name,
    ];
    [
      {args: {sortBy: 'Position', orderBy: 'Ascending'}},
      {args: {sortBy: 'Position', orderBy: 'Descending'}},
      {args: {sortBy: 'Price', orderBy: 'Descending'}},
      {args: {sortBy: 'Price', orderBy: 'Ascending'}},
    ].forEach((test, index: number) => {
      it(`should set sort by '${test.args.sortBy}' and order by '${test.args.orderBy}' in BO`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCarrier${index}`, baseContext);

        const textResult = await boShippingPreferencesPage.setCarrierSortOrderBy(page, test.args.sortBy, test.args.orderBy);
        expect(textResult).to.contain(boShippingPreferencesPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        // Click on view my shop
        page = await boShippingPreferencesPage.viewMyShop(page);
        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to shipping step in checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkFinalSummary${index}`, baseContext);

        // Go to the first product page
        await foClassicHomePage.goToProductPage(page, 1);
        // Add the product to the cart
        await foClassicProductPage.addProductToTheCart(page);
        // Proceed to checkout the shopping cart
        await foClassicCartPage.clickOnProceedToCheckout(page);
        // Checkout the order
        if (index === 0) {
          // Personal information step - Login
          await foClassicCheckoutPage.clickOnSignIn(page);
          await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
        }

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should verify the sort of carriers', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSort${index}`, baseContext);

        if (test.args.sortBy === 'Price') {
          const sortedCarriers = await foClassicCheckoutPage.getAllCarriersPrices(page);
          const expectedResult = await utilsCore.sortArray(sortedCarriers);

          if (test.args.orderBy === 'Ascending') {
            expect(sortedCarriers).to.deep.equal(expectedResult);
          } else {
            expect(sortedCarriers).to.deep.equal(expectedResult.reverse());
          }
        } else if (test.args.sortBy === 'Position') {
          const sortedCarriers = await foClassicCheckoutPage.getAllCarriersNames(page);

          if (test.args.orderBy === 'Ascending') {
            expect(sortedCarriers).to.deep.equal(sortByPosition);
          } else {
            expect(sortedCarriers).to.deep.equal(sortByPosition.reverse());
          }
        }
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        page = await foClassicCheckoutPage.closePage(browserContext, page, 0);

        const pageTitle = await boShippingPreferencesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boShippingPreferencesPage.pageTitle);
      });
    });
  });

  describe(`Disable the 2 carriers '${dataCarriers.myCheapCarrier.name}' and '${dataCarriers.myLightCarrier.name}'`, async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPageToDisable', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    carriersNames.forEach((carrierName: string, index: number) => {
      it(`should filter list by name ${carrierName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByName${index}ToDisable`, baseContext);

        await boCarriersPage.filterTable(page, 'input', 'name', carrierName);

        const textColumn = await boCarriersPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(carrierName);
      });

      it('should disable the carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `disableCarrier${index}`, baseContext);

        const isActionPerformed = await boCarriersPage.setStatus(page, 1, false);

        if (isActionPerformed) {
          const resultMessage = await boCarriersPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(boCarriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await boCarriersPage.getStatus(page, 1);
        expect(carrierStatus).to.eq(false);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDisable${index}`, baseContext);

        const numberOfCarriersAfterReset = await boCarriersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
      });
    });
  });
});
