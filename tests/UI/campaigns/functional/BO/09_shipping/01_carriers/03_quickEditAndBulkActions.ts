import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boCarriersPage,
  boCarriersCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  FakerCarrier,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsFile,
  utilsPlaywright,
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
        weightMax: 100,
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

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.generateImage(`${carrierData.name}.jpg`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile(`${carrierData.name}.jpg`);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shipping> Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);
  });

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await boCarriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriers).to.be.above(0);
  });

  it('should select all and disable them', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'selectAllAndDisable', baseContext);

    const message = await boCarriersPage.bulkSetStatus(page, 'Disable');
    expect(message).to.be.contains(boCarriersPage.successfulUpdateStatusMessage);

    for (let i = 1; i <= numberOfCarriers; i++) {
      const textColumn = await boCarriersPage.getTextColumn(
        page,
        i,
        'active',
      );
      expect(textColumn).to.equals('Disabled');
    }
  });

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openTheShopPage', baseContext);

    page = await boCarriersPage.viewMyShop(page);
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

  it('should check there are no carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNoCarriers', baseContext);

    const message = await foClassicCheckoutPage.getCarrierErrorMessage(page);
    expect(message).to.equals(foClassicCheckoutPage.noCarriersMessage);
  });

  it('should select all and enable them', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'selectAllEnable', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    const message = await boCarriersPage.bulkSetStatus(page, 'Enable');
    expect(message).to.be.contains(boCarriersPage.successfulUpdateStatusMessage);

    for (let i = 1; i <= numberOfCarriers; i++) {
      const textColumn = await boCarriersPage.getTextColumn(
        page,
        i,
        'active',
      );
      expect(textColumn).to.equals('Enabled');
    }
  });

  it('should check there are carriers', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriers', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await page.reload();

    const carrierNames = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carrierNames.length).to.equals(numberOfCarriers);
  });

  it('should select all', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'selectAll', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);

    await boCarriersPage.bulkSetSelection(page, true);

    const numSelectedBulk = await boCarriersPage.getSelectedBulkCount(page);
    expect(numSelectedBulk).to.equal(numberOfCarriers);
  });

  it('should unselect all', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'unselectAll', baseContext);

    await boCarriersPage.bulkSetSelection(page, false);

    const numSelectedBulk = await boCarriersPage.getSelectedBulkCount(page);
    expect(numSelectedBulk).to.equal(0);
  });

  it('should go to add new carrier page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

    await boCarriersPage.goToAddNewCarrierPage(page);

    const pageTitle = await boCarriersCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);
  });

  it('should create carrier and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

    const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierData);
    expect(textResult).to.contains(boCarriersPage.successfulCreationMessage);
  });

  it('should return to carriers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'returnToCarriers', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);

    const numberCarriersAfterCreation = await boCarriersPage.getNumberOfElementInGrid(page);
    expect(numberCarriersAfterCreation).to.be.equal(numberOfCarriers + 1);
  });

  it('should check there are carriers (after creation)', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersAfterCreate', baseContext);

    page = await boCarriersPage.changePage(browserContext, 1);
    await page.reload();

    const carrierNames = await foClassicCheckoutPage.getAllCarriersNames(page);
    expect(carrierNames.length).to.equals(numberOfCarriers + 1);
  });

  it('should filter list by name', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

    page = await foClassicCheckoutPage.changePage(browserContext, 0);
    await boCarriersPage.filterTable(
      page,
      'input',
      'name',
      carrierData.name,
    );

    const numberOfCarriersAfterFilter = await boCarriersPage.getNumberOfElementInGrid(page);
    expect(numberOfCarriersAfterFilter).to.equals(1);

    const textColumn = await boCarriersPage.getTextColumn(page, 1, 'name');
    expect(textColumn).to.equals(carrierData.name);
  });

  it('should delete carriers with Bulk Actions and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCarriers', baseContext);

    const deleteTextResult = await boCarriersPage.bulkDeleteCarriers(page);
    expect(deleteTextResult).to.be.contains(boCarriersPage.successfulMultiDeleteMessage);
  });

  [
    dataCarriers.myCheapCarrier,
    dataCarriers.myLightCarrier,
  ].forEach((carrier: FakerCarrier, index: number) => {
    it(`should reset in disabled mode the carrier "${carrier.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetCarrier${index}`, baseContext);

      page = await foClassicCheckoutPage.changePage(browserContext, 0);
      await boCarriersPage.filterTable(
        page,
        'input',
        'name',
        carrier.name,
      );

      const numberOfCarriersAfterFilter = await boCarriersPage.getNumberOfElementInGrid(page);
      expect(numberOfCarriersAfterFilter).to.equals(1);

      const textColumnName = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(textColumnName).to.equals(carrier.name);

      const message = await boCarriersPage.bulkSetStatus(page, 'Disable');
      expect(message).to.be.contains(boCarriersPage.successfulUpdateStatusMessage);

      const textColumnActive = await boCarriersPage.getTextColumn(page, 1, 'active');
      expect(textColumnActive).to.equals('Disabled');
    });
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

    const numberOfCarriersAfterReset = await boCarriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
  });
});
