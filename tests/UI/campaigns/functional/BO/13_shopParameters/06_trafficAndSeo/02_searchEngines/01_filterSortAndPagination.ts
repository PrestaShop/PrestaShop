import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boSearchEnginesPage,
  boSeoUrlsPage,
  type BrowserContext,
  dataSearchEngines,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_searchEngines_filterSortAndPagination';

/*
Filter search engines by id, server and get variable and reset after
Sort search engines
Check pagination limit 10 and next/previous links
 */
describe('BO - Shop Parameters - Traffic & SEO : Filter, sort and pagination search engines', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearchEngines: number = 0;

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

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.trafficAndSeoLink,
    );

    const pageTitle = await boSeoUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
  });

  it('should go to \'Search Engines\' pge', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchEnginesPage', baseContext);

    await boSeoUrlsPage.goToSearchEnginesPage(page);

    const pageTitle = await boSearchEnginesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchEnginesPage.pageTitle);
  });

  it('should reset all filters and get number of search engines in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearchEngines = await boSearchEnginesPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearchEngines).to.be.above(0);
  });

  describe('Filter search engines', async () => {
    const tests = [
      {args: {testIdentifier: 'filterId', filterBy: 'id_search_engine', filterValue: dataSearchEngines.lycos.id.toString()}},
      {args: {testIdentifier: 'filterServer', filterBy: 'server', filterValue: dataSearchEngines.google.server}},
      {args: {testIdentifier: 'filterKey', filterBy: 'query_key', filterValue: dataSearchEngines.voila.queryKey}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await boSearchEnginesPage.filterTable(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfSearchEnginesAfterFilter = await boSearchEnginesPage.getNumberOfElementInGrid(page);
        expect(numberOfSearchEnginesAfterFilter).to.be.at.most(numberOfSearchEngines);

        for (let i = 1; i <= numberOfSearchEnginesAfterFilter; i++) {
          const textColumn = await boSearchEnginesPage.getTextColumn(
            page,
            i,
            test.args.filterBy,
          );
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSearchEnginesAfterReset = await boSearchEnginesPage.resetAndGetNumberOfLines(page);
        expect(numberOfSearchEnginesAfterReset).to.equal(numberOfSearchEngines);
      });
    });
  });

  describe('Sort search engines', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_search_engine', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByServerAsc', sortBy: 'server', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByServerDesc', sortBy: 'server', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByQueryKeyAsc', sortBy: 'query_key', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByQueryKeyDesc', sortBy: 'query_key', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_search_engine', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boSearchEnginesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await boSearchEnginesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boSearchEnginesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

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

  describe('Pagination next and previous', async () => {
    it('should select 20 items by page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boSearchEnginesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should go to next page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boSearchEnginesPage.paginationNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should go to previous page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boSearchEnginesPage.paginationPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boSearchEnginesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });
});
