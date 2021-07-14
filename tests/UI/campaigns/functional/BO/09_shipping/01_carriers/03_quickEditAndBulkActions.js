require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const carriersPage = require('@pages/BO/shipping/carriers');
const addCarrierPage = require('@pages/BO/shipping/carriers/add');

// Import data
const CarrierFaker = require('@data/faker/carrier');

const baseContext = 'functional_BO_shipping_carriers_quickEditAndBulkActions';

// Browser and tab
let browserContext;
let page;

let numberOfCarriers = 0;

/*
Create 2 new carriers
Quick edit (Enable/Disable)
Bulk actions (Enable/Disable/Delete)
 */
describe('BO - Shipping - Carriers : Quick edit and bulk actions carriers', async () => {
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

  it('should go to \'Shipping> Carriers\' page', async function () {
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

  // 1 - Create 2 carriers
  describe('Create 2 carriers in BO', async () => {
    const creationTests = new Array(2).fill(0, 0, 2);
    creationTests.forEach((test, index) => {
      before(() => files.generateImage(`todelete${index}.jpg`));

      const carrierData = new CarrierFaker({name: `todelete${index}`});

      it('should go to add new carrier page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCarrierPage${index}`, baseContext);

        await carriersPage.goToAddNewCarrierPage(page);
        const pageTitle = await addCarrierPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCarrierPage.pageTitleCreate);
      });

      it(`should create carrier n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCarrier${index}`, baseContext);

        const textResult = await addCarrierPage.createEditCarrier(page, carrierData);
        await expect(textResult).to.contains(carriersPage.successfulCreationMessage);

        const numberOfCarriersAfterCreation = await carriersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCarriersAfterCreation).to.be.equal(numberOfCarriers + 1 + index);
      });

      after(() => files.deleteFile(`todelete${index}.jpg`));
    });
  });

  // 2 - Quick edit carriers
  describe('Quick edit first carrier', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForEnableDisable', baseContext);

      await carriersPage.filterTable(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfCarriersAfterFilter = await carriersPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCarriersAfterFilter; i++) {
        const textColumn = await carriersPage.getTextColumn(
          page,
          i,
          'name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} first carrier`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Carrier`, baseContext);

        const isActionPerformed = await carriersPage.setStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await carriersPage.getAlertSuccessBlockContent(page);
          await expect(resultMessage).to.contains(carriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await carriersPage.getStatus(page, 1);
        await expect(carrierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterEnableDisable', baseContext);

      const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers + 2);
    });
  });

  // 3 - Enable/Disable carriers with bulk actions
  describe('Enable/Disable carriers with bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkEnableDisable', baseContext);

      await carriersPage.filterTable(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfCarriersAfterFilter = await carriersPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCarriersAfterFilter; i++) {
        const textColumn = await carriersPage.getTextColumn(
          page,
          i,
          'name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    [
      {args: {action: 'Disable', enabledValue: false}},
      {args: {action: 'Enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} carriers with Bulk Actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ByBulkActions`, baseContext);

        // not working, skipping it
        // https://github.com/PrestaShop/PrestaShop/issues/21571
        await carriersPage.bulkSetStatus(page, test.args.action);

        // const deleteTextResult = await carriersPage.bulkEnableDisableCarriers(page, test.args.action);
        // await expect(deleteTextResult).to.be.contains(carriersPage.successfulMultiDeleteMessage);
      });
    });
  });

  // 4 - Delete the created carriers with bulk actions
  describe('Delete the created carriers with bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await carriersPage.filterTable(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfCarriersAfterFilter = await carriersPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCarriersAfterFilter; i++) {
        const textColumn = await carriersPage.getTextColumn(
          page,
          i,
          'name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete carriers with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCarriers', baseContext);

      const deleteTextResult = await carriersPage.bulkDeleteCarriers(page);
      await expect(deleteTextResult).to.be.contains(carriersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
    });
  });
});
