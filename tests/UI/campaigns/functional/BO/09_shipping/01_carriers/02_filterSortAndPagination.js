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
const {Carriers} = require('@data/demo/carriers');

const baseContext = 'functional_BO_shipping_carriers_filterSortAndPagination';

// Browser and tab
let browserContext;
let page;

let numberOfCarriers = 0;

describe('BO - Shipping - Carriers : Filter, sort and pagination carriers', async () => {
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

  // 1 - Filter carriers
  describe('Filter carriers table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_carrier',
            filterValue: Carriers.cheapCarrier.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Carriers.myCarrier.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByDelay',
            filterType: 'input',
            filterBy: 'delay',
            filterValue: Carriers.default.delay,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByStatus',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Carriers.default.status,
          },
        expected: 'Enabled',
      },
      {
        args:
          {
            testIdentifier: 'filterByFreeShipping',
            filterType: 'select',
            filterBy: 'is_free',
            filterValue: Carriers.lightCarrier.freeShipping,
          },
        expected: 'Disabled',
      },
      {
        args:
          {
            testIdentifier: 'filterByPosition',
            filterType: 'input',
            filterBy: 'a!position',
            filterValue: Carriers.lightCarrier.position,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await carriersPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCarriersAfterFilter = await carriersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCarriersAfterFilter).to.be.at.most(numberOfCarriers);

        for (let row = 1; row <= numberOfCarriersAfterFilter; row++) {
          const textColumn = await carriersPage.getTextColumn(page, row, test.args.filterBy);

          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCarriersAfterReset = await carriersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCarriersAfterReset).to.equal(numberOfCarriers);
      });
    });
  });

  // 2 - Sort carriers table
  describe('Sort carriers table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_carrier', sortDirection: 'down', isFloat: true,
        },
      },
      /* Sort by name not working, skipping it https://github.com/PrestaShop/PrestaShop/issues/21640
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'up',
        },
      }, */
      {
        args: {
          testIdentifier: 'sortByPositionAsc', sortBy: 'a!position', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionDesc', sortBy: 'a!position', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_carrier', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await carriersPage.getAllRowsColumnContent(page, test.args.sortBy);

        await carriersPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await carriersPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await carriersPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 3 - Create 16 carriers
  describe('Create 16 carriers in BO', async () => {
    const creationTests = new Array(17).fill(0, 0, 17);
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

  // 4 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await carriersPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await carriersPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await carriersPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await carriersPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 5 : Delete carriers created with bulk actions
  describe('Delete carriers with Bulk Actions', async () => {
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
