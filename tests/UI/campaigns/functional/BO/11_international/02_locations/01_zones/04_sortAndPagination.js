require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const zonesPage = require('@pages/BO/international/locations');
const addZonePage = require('@pages/BO/international/locations/add');

// Import data
const ZoneFaker = require('@data/faker/zone');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_locations_zones_sortAndPagination';

// Using chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfZones = 0;

/*
Sort zones table
Create 13 zones
Paginate between pages
Delete zones with bulk actions
 */

describe('BO - International - Zones : Sort and pagination', async () => {
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

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.locationsLink,
    );

    const pageTitle = await zonesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(zonesPage.pageTitle);
  });

  it('should reset all filters and get number of zones in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfZones = await zonesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfZones).to.be.above(0);
  });

  // 1 : Sort zones
  describe('Sort zones table', async () => {
    [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_zone', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_zone', sortDirection: 'asc', isFloat: true,
        },
      },
    ].forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await zonesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await zonesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await zonesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await zonesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 2 : Create 13 new zones
  const creationTests = new Array(13).fill(0, 0, 13);

  creationTests.forEach((test, index) => {
    describe(`Create zone n°${index + 1} in BO`, async () => {
      const createZoneData = new ZoneFaker({name: `todelete${index}`});

      it('should go to add new zone page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddZonePage${index}`, baseContext);

        await zonesPage.goToAddNewZonePage(page);
        const pageTitle = await addZonePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addZonePage.pageTitleCreate);
      });

      it('should create zone and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createZone${index}`, baseContext);

        const textResult = await addZonePage.createEditZone(page, createZoneData);
        await expect(textResult).to.contains(zonesPage.successfulCreationMessage);

        const numberOfZonesAfterCreation = await zonesPage.getNumberOfElementInGrid(page);
        await expect(numberOfZonesAfterCreation).to.be.equal(numberOfZones + 1 + index);
      });
    });
  });

  // 3 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await zonesPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await zonesPage.paginationNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await zonesPage.paginationPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await zonesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });

  // 4 : Delete zones created with bulk actions
  describe('Bulk delete zones', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await zonesPage.filterZones(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfZonesAfterFilter = await zonesPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfZonesAfterFilter; i++) {
        const textColumn = await zonesPage.getTextColumn(
          page,
          i,
          'name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should bulk delete zones', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteZones', baseContext);

      const deleteTextResult = await zonesPage.bulkDeleteZones(page);
      await expect(deleteTextResult).to.be.contains(zonesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfZonesAfterReset = await zonesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfZonesAfterReset).to.be.equal(numberOfZones);
    });
  });
});
