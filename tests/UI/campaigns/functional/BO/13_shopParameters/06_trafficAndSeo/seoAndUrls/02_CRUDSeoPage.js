require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SeoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
const AddSeoAndUrlPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls/add');

// Import data
const {orderReturn, pdfOrderReturn} = require('@data/demo/seoPages');
const SeoPageFaker = require('@data/faker/seoPage');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_TrafficAndSeo_seoAndUrls_CRUDSeoPage';

let browser;
let browserContext;
let page;

const createSeoPageData = new SeoPageFaker(orderReturn);
const editSeoPageData = new SeoPageFaker(pdfOrderReturn);
let numberOfSeoPages = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    seoAndUrlsPage: new SeoAndUrlsPage(page),
    addSeoAndUrlPage: new AddSeoAndUrlPage(page),
  };
};

describe('Create, update and delete seo page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
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

  describe('Create seo page', async () => {
    it('should go to new seo page page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSeoPage', baseContext);

      await this.pageObjects.seoAndUrlsPage.goToNewSeoUrlPage();
      const pageTitle = await this.pageObjects.addSeoAndUrlPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSeoAndUrlPage.pageTitle);
    });

    it('should create seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSeoPage', baseContext);

      const result = await this.pageObjects.addSeoAndUrlPage.createEditSeoPage(createSeoPageData);
      await expect(result).to.equal(this.pageObjects.seoAndUrlsPage.successfulCreationMessage);

      const numberOfSeoPagesAfterCreation = await this.pageObjects.seoAndUrlsPage.getNumberOfElementInGrid();
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1);
    });
  });

  describe('Update seo page', async () => {
    it('should filter by seo page name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await this.pageObjects.seoAndUrlsPage.filterTable('page', createSeoPageData.page);

      const numberOfSeoPagesAfterFilter = await this.pageObjects.seoAndUrlsPage.getNumberOfElementInGrid();
      await expect(numberOfSeoPagesAfterFilter).to.be.at.least(1);

      const textColumn = await this.pageObjects.seoAndUrlsPage.getTextColumnFromTable(1, 'page');
      await expect(textColumn).to.contains(createSeoPageData.page);
    });

    it('should go to edit first seo page page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSeoPage', baseContext);

      await this.pageObjects.seoAndUrlsPage.goToEditSeoUrlPage(1);
      const pageTitle = await this.pageObjects.addSeoAndUrlPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSeoAndUrlPage.pageTitle);
    });

    it('should edit seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSeoPage', baseContext);

      const result = await this.pageObjects.addSeoAndUrlPage.createEditSeoPage(editSeoPageData);
      await expect(result).to.equal(this.pageObjects.seoAndUrlsPage.successfulUpdateMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdate', baseContext);

      const numberOfSeoPagesAfterCreation = await this.pageObjects.seoAndUrlsPage.resetAndGetNumberOfLines();
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1);
    });
  });

  describe('Delete seo page', async () => {
    it('should filter by seo page name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await this.pageObjects.seoAndUrlsPage.filterTable('page', editSeoPageData.page);

      const numberOfSeoPagesAfterFilter = await this.pageObjects.seoAndUrlsPage.getNumberOfElementInGrid();
      await expect(numberOfSeoPagesAfterFilter).to.be.at.least(1);

      const textColumn = await this.pageObjects.seoAndUrlsPage.getTextColumnFromTable(1, 'page');
      await expect(textColumn).to.contains(editSeoPageData.page);
    });

    it('should delete seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSeoPage', baseContext);

      // delete seo page in first row
      const result = await this.pageObjects.seoAndUrlsPage.deleteSeoUrlPage(1);
      await expect(result).to.be.equal(this.pageObjects.seoAndUrlsPage.successfulDeleteMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSeoPagesAfterCreation = await this.pageObjects.seoAndUrlsPage.resetAndGetNumberOfLines();
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages);
    });
  });
});
