import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  boZonesCreatePage,
  type BrowserContext,
  FakerZone,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_locations_zones_sortAndPagination';

/*
  Sort zones table
  Create 13 zones
  Paginate between pages
  Delete zones with bulk actions
 */
describe('BO - International - Zones : Sort and pagination', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfZones: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'International > Locations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.locationsLink,
    );

    const pageTitle = await boZonesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boZonesPage.pageTitle);
  });

  it('should reset all filters and get number of zones in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfZones = await boZonesPage.resetAndGetNumberOfLines(page);
    expect(numberOfZones).to.be.above(0);
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

        const nonSortedTable = await boZonesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await boZonesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boZonesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 2 : Create 13 new zones
  const creationTests: number[] = new Array(13).fill(0, 0, 13);

  creationTests.forEach((test: number, index: number) => {
    describe(`Create zone n°${index + 1} in BO`, async () => {
      const createZoneData: FakerZone = new FakerZone({name: `todelete${index}`});

      it('should go to add new zone page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddZonePage${index}`, baseContext);

        await boZonesPage.goToAddNewZonePage(page);

        const pageTitle = await boZonesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boZonesCreatePage.pageTitleCreate);
      });

      it('should create zone and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createZone${index}`, baseContext);

        const textResult = await boZonesCreatePage.createEditZone(page, createZoneData);
        expect(textResult).to.contains(boZonesPage.successfulCreationMessage);

        const numberOfZonesAfterCreation = await boZonesPage.getNumberOfElementInGrid(page);
        expect(numberOfZonesAfterCreation).to.be.equal(numberOfZones + 1 + index);
      });
    });
  });

  // 3 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boZonesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boZonesPage.paginationNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boZonesPage.paginationPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boZonesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });

  // 4 : Delete zones created with bulk actions
  describe('Bulk delete zones', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boZonesPage.filterZones(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfZonesAfterFilter = await boZonesPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfZonesAfterFilter; i++) {
        const textColumn = await boZonesPage.getTextColumn(
          page,
          i,
          'name',
        );
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should bulk delete zones', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteZones', baseContext);

      const deleteTextResult = await boZonesPage.bulkDeleteZones(page);
      expect(deleteTextResult).to.be.contains(boZonesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfZonesAfterReset = await boZonesPage.resetAndGetNumberOfLines(page);
      expect(numberOfZonesAfterReset).to.be.equal(numberOfZones);
    });
  });
});
