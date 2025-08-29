import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boStatesPage,
  boZonesPage,
  type BrowserContext,
  dataCountries,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_locations_states_sortAndPagination';

/*
Sort states table
Paginate between pages
 */
describe('BO - International - States : Sort and pagination', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'States\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStatesPage', baseContext);

    await boZonesPage.goToSubTabStates(page);

    const pageTitle = await boStatesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStatesPage.pageTitle);
  });

  // 1 - Pagination next and previous
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boStatesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contains('(page 1 / 18)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boStatesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 18)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boStatesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 18)');
    });

    it('should change the item number to 1000 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo1000', baseContext);

      const paginationNumber = await boStatesPage.selectPaginationLimit(page, 100);
      expect(paginationNumber).to.contains('(page 1 / 4)');
    });
  });

  // 2 : Sort states table
  describe('Sort states table', async () => {
    it(`should filter by country '${dataCountries.canada.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterBeforeSort', baseContext);

      await boStatesPage.filterStates(page, 'select', 'id_country', dataCountries.canada.name);

      const paginationNumber = await boStatesPage.selectPaginationLimit(page, 100);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });

    [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_state', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryAsc', sortBy: 'name', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryDesc', sortBy: 'name', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIsoCodeAsc', sortBy: 'iso_code', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIsoCodeDesc', sortBy: 'iso_code', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCallPrefixAsc', sortBy: 'id_zone', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCallPrefixDesc', sortBy: 'id_zone', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByZoneAsc', sortBy: 'id_country', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByZoneDesc', sortBy: 'id_country', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_state', sortDirection: 'asc', isFloat: true,
        },
      },
    ].forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boStatesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await boStatesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boStatesPage.getAllRowsColumnContent(page, test.args.sortBy);

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

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterSort', baseContext);

      const numberOfStates = await boStatesPage.resetAndGetNumberOfLines(page);
      expect(numberOfStates).to.be.above(0);
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boStatesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 8)');
    });
  });
});
