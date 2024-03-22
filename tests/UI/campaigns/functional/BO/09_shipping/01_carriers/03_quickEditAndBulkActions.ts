// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import carriersPage from '@pages/BO/shipping/carriers';
import addCarrierPage from '@pages/BO/shipping/carriers/add';

import {
  // Import data
  FakerCarrier,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shipping_carriers_quickEditAndBulkActions';

/*
Create 2 new carriers
Quick edit (Enable/Disable)
Bulk actions (Enable/Disable/Delete)
 */
describe('BO - Shipping - Carriers : Quick edit and bulk actions carriers', async () => {
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

  it('should go to \'Shipping> Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shippingLink,
      dashboardPage.carriersLink,
    );

    const pageTitle = await carriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(carriersPage.pageTitle);
  });

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await carriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriers).to.be.above(0);
  });

  // 1 - Create 2 carriers
  describe('Create 2 carriers in BO', async () => {
    const creationTests: number[] = new Array(2).fill(0, 0, 2);
    creationTests.forEach((test: number, index: number) => {
      before(() => files.generateImage(`todelete${index}.jpg`));

      const carrierData: FakerCarrier = new FakerCarrier({name: `todelete${index}`});

      it('should go to add new carrier page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCarrierPage${index}`, baseContext);

        await carriersPage.goToAddNewCarrierPage(page);
        const pageTitle = await addCarrierPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCarrierPage.pageTitleCreate);
      });

      it(`should create carrier n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCarrier${index}`, baseContext);

        const textResult = await addCarrierPage.createEditCarrier(page, carrierData);
        expect(textResult).to.contains(carriersPage.successfulCreationMessage);

        const numberOfCarriersAfterCreation = await carriersPage.getNumberOfElementInGrid(page);
        expect(numberOfCarriersAfterCreation).to.be.equal(numberOfCarriers + 1 + index);
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
        expect(textColumn).to.contains('todelete');
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
          expect(resultMessage).to.contains(carriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await carriersPage.getStatus(page, 1);
        expect(carrierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterEnableDisable', baseContext);

      const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers + 2);
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
        expect(textColumn).to.contains('todelete');
      }
    });

    [
      {args: {action: 'Disable', enabledValue: false}},
      {args: {action: 'Enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} carriers with Bulk Actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ByBulkActions`, baseContext);

        const deleteTextResult = await carriersPage.bulkSetStatus(page, test.args.action);
        expect(deleteTextResult).to.be.contains(carriersPage.successfulUpdateStatusMessage);
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
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete carriers with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCarriers', baseContext);

      const deleteTextResult = await carriersPage.bulkDeleteCarriers(page);
      expect(deleteTextResult).to.be.contains(carriersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
    });
  });
});
