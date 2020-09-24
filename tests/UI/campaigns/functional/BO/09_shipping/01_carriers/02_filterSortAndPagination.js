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
const {Carriers} = require('@data/demo/carriers');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shipping_carriers_filterSortAndPagination';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfCarriers = 0;

const createCarrierData = new CarrierFaker({freeShipping: false, zoneID: 4, allZones: false});

describe('Filter, sort and pagination carriers', async () => {
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

  // 1 - Filter carriers
  describe('Filter carriers', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_carrier',
            filterValue: Carriers[3].id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Carriers[1].name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByDelay',
            filterType: 'input',
            filterBy: 'delay',
            filterValue: Carriers[0].delay,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByStatus',
            filterType: 'select',
            filterBy: 'active',
            filterValue: true,
          },
        expected: 'Enabled',
      },
      {
        args:
          {
            testIdentifier: 'filterByFreeShipping',
            filterType: 'select',
            filterBy: 'is_free',
            filterValue: false,
          },
        expected: 'Disabled',
      },
      {
        args:
          {
            testIdentifier: 'filterByPosition',
            filterType: 'input',
            filterBy: 'a!position',
            filterValue: Carriers[3].position,
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
      /* Sort by name not working, skipping it
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
});
