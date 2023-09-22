// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import seoAndUrlsPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_seoAndUrls_sortAndPaginationSeoPages';

describe('BO - Shop Parameters - Traffic & SEO : Sort and pagination seo pages', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSeoPages: number = 0;

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
    expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  it('should reset all filters and get number of SEO pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSeoPages = await seoAndUrlsPage.resetAndGetNumberOfLines(page);
    expect(numberOfSeoPages).to.be.above(0);
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

        const nonSortedTable = await seoAndUrlsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await seoAndUrlsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await seoAndUrlsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

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
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await seoAndUrlsPage.selectPaginationLimit(page, 10);
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

      const paginationNumber = await seoAndUrlsPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains(`(page 1 / ${Math.ceil(numberOfSeoPages / 50)})`);
    });
  });
});
