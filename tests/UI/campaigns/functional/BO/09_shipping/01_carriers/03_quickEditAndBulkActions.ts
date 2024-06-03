// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import carriersPage from '@pages/BO/shipping/carriers';
import addCarrierPage from '@pages/BO/shipping/carriers/add';
import {productPage} from '@pages/FO/classic/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataCarriers,
  dataCustomers,
  FakerCarrier,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shipping_carriers_quickEditAndBulkActions';

describe('BO - Shipping - Carriers : Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCarriers: number = 0;

  const carrierData: FakerCarrier = new FakerCarrier({
    name: 'test',
    transitName: 'test',
    freeShipping: false,
    ranges: [
      {
        weightMin: 0,
        weightMax: 10,
        zones: [
          {
            zone: 'all',
            price: 1,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 200,
    maxHeight: 200,
    maxDepth: 200,
    maxWeight: 500,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.generateImage(`${carrierData.name}.jpg`);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(`${carrierData.name}.jpg`);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shipping> Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await carriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(carriersPage.pageTitle);
  });

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await carriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriers).to.be.above(0);
  });

  it('should select all and disable them', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'selectAllAndDisable', baseContext);

    const message = await carriersPage.bulkSetStatus(page, 'Disable');
    expect(message).to.be.contains(carriersPage.successfulUpdateStatusMessage);

    for (let i = 1; i <= numberOfCarriers; i++) {
      const textColumn = await carriersPage.getTextColumn(
        page,
        i,
        'active',
      );
      expect(textColumn).to.equals('Disabled');
    }
  });

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openTheShopPage', baseContext);

    page = await carriersPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should add the first product to the cart and checkout', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

    // Go to the first product page
    await foClassicHomePage.goToProductPage(page, 1);
    // Add the product to the cart
    await productPage.addProductToTheCart(page);
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

  it('should check there are no carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNoCarriers', baseContext);

    const message = await foClassicCheckoutPage.getCarrierErrorMessage(page);
    expect(message).to.equals(foClassicCheckoutPage.noCarriersMessage);
  });

  it('should select all and enable them', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'selectAllEnable', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    const message = await carriersPage.bulkSetStatus(page, 'Enable');
    expect(message).to.be.contains(carriersPage.successfulUpdateStatusMessage);

    for (let i = 1; i <= numberOfCarriers; i++) {
      const textColumn = await carriersPage.getTextColumn(
        page,
        i,
        'active',
      );
      expect(textColumn).to.equals('Enabled');
    }
  });

  it('should check there are carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriers', baseContext);

    page = await carriersPage.changePage(browserContext, 1);
    await page.reload();

    const carrierNames = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carrierNames.length).to.equals(numberOfCarriers);
  });

  it('should go to add new carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    await carriersPage.goToAddNewCarrierPage(page);

    const pageTitle = await addCarrierPage.getPageTitle(page);
    expect(pageTitle).to.contains(addCarrierPage.pageTitleCreate);
  });

  it('should create carrier and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

    const textResult = await addCarrierPage.createEditCarrier(page, carrierData);
    expect(textResult).to.contains(carriersPage.successfulCreationMessage);

    const numberOfCarriersAfterCreation = await carriersPage.getNumberOfElementInGrid(page);
    expect(numberOfCarriersAfterCreation).to.be.equal(numberOfCarriers + 1);
  });

  it('should check there are carriers (after creation)', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersAfterCreate', baseContext);

    page = await carriersPage.changePage(browserContext, 1);
    await page.reload();

    const carrierNames = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carrierNames.length).to.equals(numberOfCarriers + 1);
  });

  it('should filter list by name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);
    await carriersPage.filterTable(
      page,
      'input',
      'name',
      carrierData.name,
    );

    const numberOfCarriersAfterFilter = await carriersPage.getNumberOfElementInGrid(page);
    expect(numberOfCarriersAfterFilter).to.equals(1);

    const textColumn = await carriersPage.getTextColumn(page, 1, 'name');
    expect(textColumn).to.equals(carrierData.name);
  });

  it('should delete carriers with Bulk Actions and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCarriers', baseContext);

    const deleteTextResult = await carriersPage.bulkDeleteCarriers(page);
    expect(deleteTextResult).to.be.contains(carriersPage.successfulMultiDeleteMessage);
  });

  [
    dataCarriers.myCheapCarrier,
    dataCarriers.myLightCarrier,
  ].forEach((carrier: FakerCarrier, index: number) => {
    it(`should reset in disabled mode the carrier "${carrier.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetCarrier${index}`, baseContext);

      page = await foClassicCheckoutPage.changePage(browserContext, 0);
      await carriersPage.filterTable(
        page,
        'input',
        'name',
        carrier.name,
      );

      const numberOfCarriersAfterFilter = await carriersPage.getNumberOfElementInGrid(page);
      expect(numberOfCarriersAfterFilter).to.equals(1);

      const textColumnName = await carriersPage.getTextColumn(page, 1, 'name');
      expect(textColumnName).to.equals(carrier.name);

      const message = await carriersPage.bulkSetStatus(page, 'Disable');
      expect(message).to.be.contains(carriersPage.successfulUpdateStatusMessage);

      const textColumnActive = await carriersPage.getTextColumn(page, 1, 'active');
      expect(textColumnActive).to.equals('Disabled');
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

    const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
  });
});
