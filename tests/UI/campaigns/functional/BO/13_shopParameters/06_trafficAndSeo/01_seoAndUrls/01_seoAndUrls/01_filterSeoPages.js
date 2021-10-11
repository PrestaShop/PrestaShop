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

// Import data
const {contact} = require('@data/demo/seoPages');

const baseContext = 'functional_BO_shopParameters_TrafficAndSeo_seoAndUrls_filterSeoPages';

let browserContext;
let page;
let numberOfSeoPages = 0;

/*
Filter SEO pages with id, page, page title and friendly url
 */
describe('BO - Shop Parameters - Traffic & SEO : Filter SEO pages with id, page, page title and '
  + 'friendly url', async () => {
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
    await expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  it('should reset all filters and get number of SEO pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSeoPages = await seoAndUrlsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfSeoPages).to.be.above(0);
  });

  describe('Filter SEO pages', async () => {
    const tests = [
      {args: {testIdentifier: 'filterIdMeta', filterBy: 'id_meta', filterValue: contact.id}},
      {args: {testIdentifier: 'filterPage', filterBy: 'page', filterValue: contact.page}},
      {args: {testIdentifier: 'filterTitle', filterBy: 'title', filterValue: contact.title}},
      {args: {testIdentifier: 'filterUrlRewrite', filterBy: 'url_rewrite', filterValue: contact.friendlyUrl}},
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
        await expect(numberOfSeoPagesAfterFilter).to.be.at.most(numberOfSeoPages);

        for (let i = 1; i <= numberOfSeoPagesAfterFilter; i++) {
          const textColumn = await seoAndUrlsPage.getTextColumnFromTable(page, i, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSeoPagesAfterReset = await seoAndUrlsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfSeoPagesAfterReset).to.equal(numberOfSeoPages);
      });
    });
  });
});
