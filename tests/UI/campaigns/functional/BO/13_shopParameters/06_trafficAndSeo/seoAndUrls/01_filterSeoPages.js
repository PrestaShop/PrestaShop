require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SeoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');

// Import data
const {contact} = require('@data/demo/seoPages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_TrafficAndSeo_seoAndUrls_filterSeoPages';

let browserContext;
let page;
let numberOfSeoPages = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    seoAndUrlsPage: new SeoAndUrlsPage(page),
  };
};

/*
Filter SEO pages with id, page, page title and friendly url
 */
describe('Filter SEO pages with id, page, page title and friendly url', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to contact page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > SEO and Urls\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.trafficAndSeoLink,
    );

    await this.pageObjects.seoAndUrlsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.seoAndUrlsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.seoAndUrlsPage.pageTitle);
  });

  it('should reset all filters and get number of SEO pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSeoPages = await this.pageObjects.seoAndUrlsPage.resetAndGetNumberOfLines();
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

        await this.pageObjects.seoAndUrlsPage.filterTable(
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfSeoPagesAfterFilter = await this.pageObjects.seoAndUrlsPage.getNumberOfElementInGrid();
        await expect(numberOfSeoPagesAfterFilter).to.be.at.most(numberOfSeoPages);

        for (let i = 1; i <= numberOfSeoPagesAfterFilter; i++) {
          const textColumn = await this.pageObjects.seoAndUrlsPage.getTextColumnFromTable(
            i,
            test.args.filterBy,
          );

          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfSeoPagesAfterReset = await this.pageObjects.seoAndUrlsPage.resetAndGetNumberOfLines();
        await expect(numberOfSeoPagesAfterReset).to.equal(numberOfSeoPages);
      });
    });
  });
});
