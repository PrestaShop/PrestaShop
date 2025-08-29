import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boSeoUrlsPage,
  type BrowserContext,
  dataSeoPages,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    await boSeoUrlsPage.closeSfToolBar(page);

    const pageTitle = await boSeoUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
  });

  it('should reset all filters and get number of SEO pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSeoPages = await boSeoUrlsPage.resetAndGetNumberOfLines(page);
    expect(numberOfSeoPages).to.be.above(0);
  });

  describe('Filter SEO pages', async () => {
    const tests = [
      {args: {testIdentifier: 'filterIdMeta', filterBy: 'id_meta', filterValue: dataSeoPages.contact.id.toString()}},
      {args: {testIdentifier: 'filterPage', filterBy: 'page', filterValue: dataSeoPages.contact.page}},
      {args: {testIdentifier: 'filterTitle', filterBy: 'title', filterValue: dataSeoPages.contact.title}},
      {args: {testIdentifier: 'filterUrlRewrite', filterBy: 'url_rewrite', filterValue: dataSeoPages.contact.friendlyUrl}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

        await boSeoUrlsPage.filterTable(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfSeoPagesAfterFilter = await boSeoUrlsPage.getNumberOfElementInGrid(page);
        expect(numberOfSeoPagesAfterFilter).to.be.at.most(numberOfSeoPages);

        for (let i = 1; i <= numberOfSeoPagesAfterFilter; i++) {
          const textColumn = await boSeoUrlsPage.getTextColumnFromTable(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSeoPagesAfterReset = await boSeoUrlsPage.resetAndGetNumberOfLines(page);
        expect(numberOfSeoPagesAfterReset).to.equal(numberOfSeoPages);
      });
    });
  });
});
