require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const carriersPage = require('@pages/BO/shipping/carriers');
const addCarrierPage = require('@pages/BO/shipping/carriers/add');

// Import data
const CarrierFaker = require('@data/faker/carrier');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shipping_carriers_CRUDCarrier';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfCarriers = 0;

const createCarrierData = new CarrierFaker();
const editCarrierData = new CarrierFaker();


describe('Create, update and delete carrier in BO', async () => {
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

  it('should go to \'Shipping/Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.carriersLink,
    );

    const pageTitle = await carriersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(carriersPage.pageTitle);
  });

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await carriersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCarriers).to.be.above(0);
  });

  describe('Create carrier in BO', async () => {
    it('should go to add new carrier page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCarrierPage', baseContext);

      await carriersPage.addNewCarrierLink(page);
      const pageTitle = await addCarrierPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCarrierPage.pageTitleCreate);
    });

    it('should create carrier and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCarrier', baseContext);

      const textResult = await addCarrierPage.createEditCarrier(page, createCarrierData);
      await expect(textResult).to.contains(carriersPage.successfulCreationMessage);

      const numberCarriersAfterCreation = await carriersPage.getNumberOfElementInGrid(page);
      await expect(numberCarriersAfterCreation).to.be.equal(numberOfCarriers + 1);
    });
  });

  /*describe('Update image type created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await carrierData.resetFilter(page);

      await carrierData.filterTable(
        page,
        'input',
        'name',
        createCarrierData.name,
      );

      const textEmail = await carrierData.getTextColumn(page, 1, 'name');
      await expect(textEmail).to.contains(createCarrierData.name);
    });

    it('should go to edit image type page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditImageTypePage', baseContext);

      await carrierData.gotoEditImageTypePage(page, 1);
      const pageTitle = await addCarrierPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCarrierPage.pageTitleEdit);
    });

    it('should update image type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateImageType', baseContext);

      const textResult = await addCarrierPage.createEditImageType(page, editCarrierData);
      await expect(textResult).to.contains(carrierData.successfulUpdateMessage);

      const numberOfImageTypesAfterUpdate = await carrierData.resetAndGetNumberOfLines(page);
      await expect(numberOfImageTypesAfterUpdate).to.be.equal(numberOfImageTypes + 1);
    });
  });

  describe('Delete image type', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await carrierData.resetFilter(page);

      await carrierData.filterTable(
        page,
        'input',
        'name',
        editCarrierData.name,
      );

      const textEmail = await carrierData.getTextColumn(page, 1, 'name');
      await expect(textEmail).to.contains(editCarrierData.name);
    });

    it('should delete image type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteImageType', baseContext);

      const textResult = await carrierData.deleteImageType(page, 1);
      await expect(textResult).to.contains(carrierData.successfulDeleteMessage);

      const numberOfImageTypesAfterDelete = await carrierData.resetAndGetNumberOfLines(page);
      await expect(numberOfImageTypesAfterDelete).to.be.equal(numberOfImageTypes);
    });
  });*/
});
