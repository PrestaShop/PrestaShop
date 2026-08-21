// Import utils
import testContext from '@utils/testContext';

import {
  boCarriersPage,
  boCarriersCreatePage,
  boLoginPage,
  type BrowserContext,
  dataZones,
  FakerCarrier,
  boDashboardPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_shipping_carriers_billingByShopConfiguration';

/*
Create a carrier billed based on the shop configuration
Reopen the carrier edit page and check the billing option is still the shop configuration
Delete carrier
 */
describe('BO - Shipping - Carriers : Billing based on the shop configuration', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCarriers: number = 0;

  const createCarrierData: FakerCarrier = new FakerCarrier({
    // General settings
    name: 'Carrier Config Based',
    // Shipping locations and cost
    handlingCosts: false,
    freeShipping: false,
    billing: 'Based on the shop configuration',
    taxRule: 'No tax',
    outOfRangeBehavior: 'Apply the cost of the highest defined range',
    ranges: [
      {
        weightMin: 0,
        weightMax: 50,
        zones: [
          {
            zone: dataZones.europe,
            price: 5,
          },
        ],
      },
    ],
    // Size weight and group access
    maxWidth: 100,
    maxHeight: 100,
    maxDepth: 100,
    maxWeight: 100,
    enable: true,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.generateImage(`${createCarrierData.name}.jpg`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(`${createCarrierData.name}.jpg`);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
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

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await boCarriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriers).to.be.above(0);
  });

  describe('Create carrier billed based on the shop configuration', async () => {
    it('should go to add new carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

      await boCarriersPage.goToAddNewCarrierPage(page);

      const pageTitle = await boCarriersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);
    });

    it('should create carrier and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

      const textResult = await boCarriersCreatePage.createEditCarrier(page, createCarrierData);
      expect(textResult).to.contains(boCarriersPage.successfulCreationMessage);
    });
  });

  describe('Check the billing option is kept on the edit page', async () => {
    it('should return to carriers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToCarriers', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckCreatedCarrier', baseContext);

      await boCarriersPage.resetFilter(page);
      await boCarriersPage.filterTable(
        page,
        'input',
        'name',
        createCarrierData.name,
      );

      const name = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(name).to.contains(createCarrierData.name);
    });

    it('should go to edit carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCarrierPage', baseContext);

      await boCarriersPage.goToEditCarrierPage(page, 1);

      const pageTitle = await boCarriersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleEdit);
    });

    it('should check that the billing option is still based on the shop configuration', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBillingSelection', baseContext);

      const billingSelection = await boCarriersCreatePage.getBillingSelection(page);
      expect(billingSelection).to.equal(createCarrierData.billing);
    });
  });

  describe('Delete carrier', async () => {
    it('should go to \'Shipping > Carriers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPageForDelete', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shippingLink,
        boDashboardPage.carriersLink,
      );

      const pageTitle = await boCarriersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCarriersPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boCarriersPage.resetFilter(page);
      await boCarriersPage.filterTable(
        page,
        'input',
        'name',
        createCarrierData.name,
      );

      const carrierName = await boCarriersPage.getTextColumn(page, 1, 'name');
      expect(carrierName).to.contains(createCarrierData.name);
    });

    it('should delete carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCarrier', baseContext);

      const textResult = await boCarriersPage.deleteCarrier(page, 1);
      expect(textResult).to.contains(boCarriersPage.successfulDeleteMessage);

      const numberOfCarriersAfterDelete = await boCarriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriersAfterDelete).to.be.equal(numberOfCarriers);
    });
  });
});
