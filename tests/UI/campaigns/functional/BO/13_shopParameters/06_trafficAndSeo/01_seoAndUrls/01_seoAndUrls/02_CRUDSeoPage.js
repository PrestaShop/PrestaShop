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
const addSeoAndUrlPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls/add');

// Import data
const {orderReturn, pdfOrderReturn} = require('@data/demo/seoPages');
const SeoPageFaker = require('@data/faker/seoPage');

const baseContext = 'functional_BO_shopParameters_TrafficAndSeo_seoAndUrls_CRUDSeoPage';

let browserContext;
let page;

const createSeoPageData = new SeoPageFaker(orderReturn);
const editSeoPageData = new SeoPageFaker(pdfOrderReturn);
let numberOfSeoPages = 0;

describe('BO - Shop Parameters - Traffic & SEO : Create, update and delete seo page', async () => {
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

  describe('Create seo page', async () => {
    it('should go to new seo page page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSeoPage', baseContext);

      await seoAndUrlsPage.goToNewSeoUrlPage(page);
      const pageTitle = await addSeoAndUrlPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addSeoAndUrlPage.pageTitle);
    });

    it('should create seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSeoPage', baseContext);

      const result = await addSeoAndUrlPage.createEditSeoPage(page, createSeoPageData);
      await expect(result).to.equal(seoAndUrlsPage.successfulCreationMessage);

      const numberOfSeoPagesAfterCreation = await seoAndUrlsPage.getNumberOfElementInGrid(page);
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1);
    });
  });

  describe('Update seo page', async () => {
    it('should filter by seo page name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await seoAndUrlsPage.filterTable(page, 'page', createSeoPageData.page);

      const numberOfSeoPagesAfterFilter = await seoAndUrlsPage.getNumberOfElementInGrid(page);
      await expect(numberOfSeoPagesAfterFilter).to.be.at.least(1);

      const textColumn = await seoAndUrlsPage.getTextColumnFromTable(page, 1, 'page');
      await expect(textColumn).to.contains(createSeoPageData.page);
    });

    it('should go to edit first seo page page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSeoPage', baseContext);

      await seoAndUrlsPage.goToEditSeoUrlPage(page, 1);
      const pageTitle = await addSeoAndUrlPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addSeoAndUrlPage.pageTitle);
    });

    it('should edit seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSeoPage', baseContext);

      const result = await addSeoAndUrlPage.createEditSeoPage(page, editSeoPageData);
      await expect(result).to.equal(seoAndUrlsPage.successfulUpdateMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdate', baseContext);

      const numberOfSeoPagesAfterCreation = await seoAndUrlsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1);
    });
  });

  describe('Delete seo page', async () => {
    it('should filter by seo page name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await seoAndUrlsPage.filterTable(page, 'page', editSeoPageData.page);

      const numberOfSeoPagesAfterFilter = await seoAndUrlsPage.getNumberOfElementInGrid(page);
      await expect(numberOfSeoPagesAfterFilter).to.be.at.least(1);

      const textColumn = await seoAndUrlsPage.getTextColumnFromTable(page, 1, 'page');
      await expect(textColumn).to.contains(editSeoPageData.page);
    });

    it('should delete seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSeoPage', baseContext);

      // delete seo page in first row
      const result = await seoAndUrlsPage.deleteSeoUrlPage(page, 1);
      await expect(result).to.be.equal(seoAndUrlsPage.successfulDeleteMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSeoPagesAfterCreation = await seoAndUrlsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages);
    });
  });
});
