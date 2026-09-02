import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCountriesPage,
  boDashboardPage,
  boLoginPage,
  boZonesPage,
  type BrowserContext,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_locations_countries_sortAndPagination';

/*
 * Sort countries table
 * Paginate between pages
 */
describe('BO - International - Countries : Sort and pagination', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Countries\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

    await boZonesPage.goToSubTabCountries(page);

    const pageTitle = await boCountriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCountriesPage.pageTitle);
  });

  // 1 - Pagination next and previous
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boCountriesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contains('(page 1 / 13)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boCountriesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 13)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boCountriesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 13)');
    });

    it('should change the item number to 300 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo300', baseContext);

      const paginationNumber = await boCountriesPage.selectPaginationLimit(page, 300);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 2 : Sort countries table
  describe('Sort countries table', async () => {
    [
      {
        testIdentifier: 'sortByIdDesc',
        sortBy: 'id_country',
        sortDirection: 'desc',
        isFloat: true,
      },
      {
        testIdentifier: 'sortByCountryAsc',
        sortBy: 'name',
        sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByCountryDesc',
        sortBy: 'name',
        sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByIsoCodeAsc',
        sortBy: 'iso_code',
        sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByIsoCodeDesc',
        sortBy: 'iso_code',
        sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByCallPrefixAsc',
        sortBy: 'call_prefix',
        sortDirection: 'asc',
        isFloat: true,
      },
      {
        testIdentifier: 'sortByCallPrefixDesc',
        sortBy: 'call_prefix',
        sortDirection: 'desc',
        isFloat: true,
      },
      {
        testIdentifier: 'sortByZoneAsc',
        sortBy: 'zone_name',
        sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByZoneDesc',
        sortBy: 'zone_name',
        sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByIdAsc',
        sortBy: 'id_country',
        sortDirection: 'asc',
        isFloat: true,
      },
    ].forEach((test) => {
      it(`should sort by '${test.sortBy}' '${test.sortDirection}' And check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        const nonSortedTable = await boCountriesPage.getAllRowsColumnContent(page, test.sortBy);

        await boCountriesPage.sortTable(page, test.sortBy, test.sortDirection);

        const sortedTable = await boCountriesPage.getAllRowsColumnContent(page, test.sortBy);

        if (test.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boCountriesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 5)');
    });
  });
});
