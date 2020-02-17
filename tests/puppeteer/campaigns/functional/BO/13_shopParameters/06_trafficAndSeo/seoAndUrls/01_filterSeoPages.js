require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SeoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
// Importing data
const {contact} = require('@data/demo/seoPages');

let browser;
let page;
let numberOfSeoPages = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
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
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to contact page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > SEO and Urls\' page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.trafficAndSeoLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.seoAndUrlsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.seoAndUrlsPage.pageTitle);
  });

  it('should reset all filters and get number of SEO pages in BO', async function () {
    numberOfSeoPages = await this.pageObjects.seoAndUrlsPage.resetAndGetNumberOfLines();
    await expect(numberOfSeoPages).to.be.above(0);
  });

  describe('Filter SEO pages', async () => {
    const tests = [
      {args: {filterBy: 'id_meta', filterValue: contact.id}},
      {args: {filterBy: 'page', filterValue: contact.page}},
      {args: {filterBy: 'title', filterValue: contact.title}},
      {args: {filterBy: 'url_rewrite', filterValue: contact.friendlyUrl}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
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
        const numberOfSeoPagesAfterReset = await this.pageObjects.seoAndUrlsPage.resetAndGetNumberOfLines();
        await expect(numberOfSeoPagesAfterReset).to.equal(numberOfSeoPages);
      });
    });
  });
});
