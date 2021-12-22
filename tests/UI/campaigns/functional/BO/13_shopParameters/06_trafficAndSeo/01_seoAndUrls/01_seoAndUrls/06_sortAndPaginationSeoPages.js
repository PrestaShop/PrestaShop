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

const baseContext = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_sortAndPaginationSeoPages';

let numberOfSeoPages = 0;

let browserContext;
let page;

describe('BO - Shop Parameters - Traffic & SEO : Sort and pagination seo pages', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.trafficAndSeoLink,
    );

    await seoAndUrlsPage.closeSfToolBar(page);

    const pageTitle = await seoAndUrlsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  it('should reset all filters and get number of SEO pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSeoPages = await seoAndUrlsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfSeoPages).to.be.above(0);
  });

  describe('Sort seo pages', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_meta', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByPageAsc', sortBy: 'page', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByPageDesc', sortBy: 'page', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByTitleAsc', sortBy: 'title', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByTitleDesc', sortBy: 'title', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByFriendlyUrlAsc', sortBy: 'url_rewrite', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortFriendlyUrlDesc', sortBy: 'url_rewrite', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_meta', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await seoAndUrlsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await seoAndUrlsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await seoAndUrlsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await seoAndUrlsPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await seoAndUrlsPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains(`(page 1 / ${Math.ceil(numberOfSeoPages / 10)})`);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await seoAndUrlsPage.paginationNext(page);
      expect(paginationNumber).to.contains(`(page 2 / ${Math.ceil(numberOfSeoPages / 10)})`);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await seoAndUrlsPage.paginationPrevious(page);
      expect(paginationNumber).to.contains(`(page 1 / ${Math.ceil(numberOfSeoPages / 10)})`);
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await seoAndUrlsPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains(`(page 1 / ${Math.ceil(numberOfSeoPages / 50)})`);
    });
  });
});
