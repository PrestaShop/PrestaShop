require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

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

const baseContext = 'functional_BO_shipping_carriers_quickEditAndBulkActions';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfCarriers = 0;

describe('Quick edit and bulk actions carriers', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated images */
    for (let i = 0; i <= 2; i++) {
      await files.deleteFile(`todelete${i}.jpg`);
    }
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

  // 1 - Create 2 carriers
  const creationTests = new Array(2).fill(0, 0, 2);

  creationTests.forEach((test, index) => {
    describe(`Create carrier n°${index + 1} in BO`, async () => {
      const carrierData = new CarrierFaker({name: `todelete${index}`});

      it('should go to add new carrier page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCarrierPage${index}`, baseContext);

        await carriersPage.goToAddNewCarrierPage(page);
        const pageTitle = await addCarrierPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCarrierPage.pageTitleCreate);
      });

      it('should create carrier and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCarrier${index}`, baseContext);

        const textResult = await addCarrierPage.createEditCarrier(page, carrierData);
        await expect(textResult).to.contains(carriersPage.successfulCreationMessage);

        const numberOfCarriersAfterCreation = await carriersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCarriersAfterCreation).to.be.equal(numberOfCarriers + 1 + index);
      });
    });
  });

  // 2 - Quick edit carriers
  describe('Quick edit first carrier', async () => {
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

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first carrier`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Carrier`, baseContext);

        const isActionPerformed = await carriersPage.updateEnabledValue(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await carriersPage.getTextContent(
            page,
            carriersPage.alertSuccessBlock,
          );
          await expect(resultMessage).to.contains(carriersPage.successfulUpdateStatusMessage);
        }

        const carrierStatus = await carriersPage.getToggleColumnValue(page, 1);
        await expect(carrierStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers + 2);
    });
  });
  // 3 - Delete the created carriers with bulk actions
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
