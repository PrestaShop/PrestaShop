// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import seoAndUrlsPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls';

// Import data
import SeoPages from '@data/demo/seoPages';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_seoAndUrls_filterSeoPages';

/*
Filter SEO pages with id, page, page title and friendly url
 */
describe('BO - Shop Parameters - Traffic & SEO : Filter SEO pages with id, page, page title and '
  + 'friendly url', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

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

  describe('Filter SEO pages', async () => {
    const tests = [
      {args: {testIdentifier: 'filterIdMeta', filterBy: 'id_meta', filterValue: SeoPages.contact.id.toString()}},
      {args: {testIdentifier: 'filterPage', filterBy: 'page', filterValue: SeoPages.contact.page}},
      {args: {testIdentifier: 'filterTitle', filterBy: 'title', filterValue: SeoPages.contact.title}},
      {args: {testIdentifier: 'filterUrlRewrite', filterBy: 'url_rewrite', filterValue: SeoPages.contact.friendlyUrl}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await seoAndUrlsPage.filterTable(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfSeoPagesAfterFilter = await seoAndUrlsPage.getNumberOfElementInGrid(page);
        expect(numberOfSeoPagesAfterFilter).to.be.at.most(numberOfSeoPages);

        for (let i = 1; i <= numberOfSeoPagesAfterFilter; i++) {
          const textColumn = await seoAndUrlsPage.getTextColumnFromTable(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSeoPagesAfterReset = await seoAndUrlsPage.resetAndGetNumberOfLines(page);
        expect(numberOfSeoPagesAfterReset).to.equal(numberOfSeoPages);
      });
    });
  });
});
