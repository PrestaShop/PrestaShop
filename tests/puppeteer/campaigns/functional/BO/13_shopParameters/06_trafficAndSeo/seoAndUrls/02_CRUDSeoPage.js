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
const AddSeoAndUrlPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls/add');
// Importing data
const {orderReturn, pdfOrderReturn} = require('@data/demo/seoPages');
const SeoPageFaker = require('@data/faker/seoPage');

let browser;
let page;
const createSeoPageData = new SeoPageFaker(orderReturn);
const editSeoPageData = new SeoPageFaker(pdfOrderReturn);
let numberOfSeoPages = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    seoAndUrlsPage: new SeoAndUrlsPage(page),
    addSeoAndUrlPage: new AddSeoAndUrlPage(page),
  };
};

describe('Create, update and delete seo page', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
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

  describe('Create seo page', async () => {
    it('should go to new seo page page', async function () {
      await this.pageObjects.seoAndUrlsPage.goToNewSeoUrlPage();
      const pageTitle = await this.pageObjects.addSeoAndUrlPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSeoAndUrlPage.pageTitle);
    });

    it('should create seo page', async function () {
      const result = await this.pageObjects.addSeoAndUrlPage.createEditSeoPage(createSeoPageData);
      await expect(result).to.equal(this.pageObjects.seoAndUrlsPage.successfulCreationMessage);
      const numberOfSeoPagesAfterCreation = await this.pageObjects.seoAndUrlsPage.getNumberOfElementInGrid();
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1);
    });
  });

  describe('Update seo page', async () => {
    it('should filter by seo page name', async function () {
      await this.pageObjects.seoAndUrlsPage.filterTable('page', createSeoPageData.page);
      const numberOfSeoPagesAfterFilter = await this.pageObjects.seoAndUrlsPage.getNumberOfElementInGrid();
      await expect(numberOfSeoPagesAfterFilter).to.be.at.least(1);
      const textColumn = await this.pageObjects.seoAndUrlsPage.getTextColumnFromTable(1, 'page');
      await expect(textColumn).to.contains(createSeoPageData.page);
    });

    it('should go to edit first seo page page', async function () {
      await this.pageObjects.seoAndUrlsPage.goToEditSeoUrlPage(1);
      const pageTitle = await this.pageObjects.addSeoAndUrlPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addSeoAndUrlPage.pageTitle);
    });

    it('should edit seo page', async function () {
      const result = await this.pageObjects.addSeoAndUrlPage.createEditSeoPage(editSeoPageData);
      await expect(result).to.equal(this.pageObjects.seoAndUrlsPage.successfulUpdateMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      const numberOfSeoPagesAfterCreation = await this.pageObjects.seoAndUrlsPage.resetAndGetNumberOfLines();
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1);
    });
  });

  describe('Delete seo page', async () => {
    it('should filter by seo page name', async function () {
      await this.pageObjects.seoAndUrlsPage.filterTable('page', editSeoPageData.page);
      const numberOfSeoPagesAfterFilter = await this.pageObjects.seoAndUrlsPage.getNumberOfElementInGrid();
      await expect(numberOfSeoPagesAfterFilter).to.be.at.least(1);
      const textColumn = await this.pageObjects.seoAndUrlsPage.getTextColumnFromTable(1, 'page');
      await expect(textColumn).to.contains(editSeoPageData.page);
    });

    it('should delete seo page', async function () {
      // delete seo page in first row
      const result = await this.pageObjects.seoAndUrlsPage.deleteSeoUrlPage(1);
      await expect(result).to.be.equal(this.pageObjects.seoAndUrlsPage.successfulDeleteMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      const numberOfSeoPagesAfterCreation = await this.pageObjects.seoAndUrlsPage.resetAndGetNumberOfLines();
      await expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages);
    });
  });
});
