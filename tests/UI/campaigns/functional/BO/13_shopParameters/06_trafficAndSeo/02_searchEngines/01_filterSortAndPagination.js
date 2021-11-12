require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const seoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
const searchEnginesPage = require('@pages/BO/shopParameters/trafficAndSeo/searchEngines');

// Import data
const {searchEngines} = require('@data/demo/searchEngines');

const baseContext = 'functional_BO_shopParameters_trafficAndSeo_searchEngines_filterSortAndPagination';

let browserContext;
let page;
let numberOfSearchEngines = 0;

/*
Filter search engines by id, server and get variable and reset after
Sort search engines
Check pagination limit 10 and next/previous links
 */
describe('BO - Shop Parameters - Traffic & SEO : Filter, sort and pagination search engines', async () => {
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

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.trafficAndSeoLink,
    );

    const pageTitle = await seoAndUrlsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  it('should go to \'Search Engines\' pge', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchEnginesPage', baseContext);

    await seoAndUrlsPage.goToSearchEnginesPage(page);

    const pageTitle = await searchEnginesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(searchEnginesPage.pageTitle);
  });

  it('should reset all filters and get number of search engines in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearchEngines = await searchEnginesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfSearchEngines).to.be.above(0);
  });

  describe('Filter search engines', async () => {
    const tests = [
      {args: {testIdentifier: 'filterId', filterBy: 'id_search_engine', filterValue: searchEngines.lycos.id}},
      {args: {testIdentifier: 'filterServer', filterBy: 'server', filterValue: searchEngines.google.server}},
      {args: {testIdentifier: 'filterKey', filterBy: 'query_key', filterValue: searchEngines.voila.queryKey}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await searchEnginesPage.filterTable(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfSearchEnginesAfterFilter = await searchEnginesPage.getNumberOfElementInGrid(page);
        await expect(numberOfSearchEnginesAfterFilter).to.be.at.most(numberOfSearchEngines);

        for (let i = 1; i <= numberOfSearchEnginesAfterFilter; i++) {
          const textColumn = await searchEnginesPage.getTextColumn(
            page,
            i,
            test.args.filterBy,
          );

          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSearchEnginesAfterReset = await searchEnginesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfSearchEnginesAfterReset).to.equal(numberOfSearchEngines);
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

        let nonSortedTable = await searchEnginesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await searchEnginesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await searchEnginesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await searchEnginesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  describe('Pagination next and previous', async () => {
    it('should select 20 items by page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await searchEnginesPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should go to next page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await searchEnginesPage.paginationNext(page);
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should go to previous page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await searchEnginesPage.paginationPrevious(page);
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await searchEnginesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });
});
