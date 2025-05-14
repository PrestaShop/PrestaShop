import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boSeoUrlsPage,
  boSeoUrlsCreatePage,
  type BrowserContext,
  dataSeoPages,
  FakerSeoPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_seoAndUrls_CRUDSeoPage';

describe('BO - Shop Parameters - Traffic & SEO : Create, update and delete seo page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSeoPages: number = 0;

  const createSeoPageData: FakerSeoPage = dataSeoPages.orderReturn;
  const editSeoPageData: FakerSeoPage = dataSeoPages.pdfOrderReturn;

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

  describe('Create seo page', async () => {
    it('should go to new seo page page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewSeoPage', baseContext);

      await boSeoUrlsPage.goToNewSeoUrlPage(page);

      const pageTitle = await boSeoUrlsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSeoUrlsCreatePage.pageTitle);
    });

    it('should create seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSeoPage', baseContext);

      const result = await boSeoUrlsCreatePage.createEditSeoPage(page, createSeoPageData);
      expect(result).to.equal(boSeoUrlsPage.successfulCreationMessage);

      const numberOfSeoPagesAfterCreation = await boSeoUrlsPage.getNumberOfElementInGrid(page);
      expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1);
    });
  });

  describe('Update seo page', async () => {
    it('should filter by seo page name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await boSeoUrlsPage.filterTable(page, 'page', createSeoPageData.page);

      const numberOfSeoPagesAfterFilter = await boSeoUrlsPage.getNumberOfElementInGrid(page);
      expect(numberOfSeoPagesAfterFilter).to.be.at.least(1);

      const textColumn = await boSeoUrlsPage.getTextColumnFromTable(page, 1, 'page');
      expect(textColumn).to.contains(createSeoPageData.page);
    });

    it('should go to edit first seo page page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditSeoPage', baseContext);

      await boSeoUrlsPage.goToEditSeoUrlPage(page, 1);

      const pageTitle = await boSeoUrlsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boSeoUrlsCreatePage.editPageTitle);
    });

    it('should edit seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editSeoPage', baseContext);

      const result = await boSeoUrlsCreatePage.createEditSeoPage(page, editSeoPageData);
      expect(result).to.equal(boSeoUrlsPage.successfulUpdateMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdate', baseContext);

      const numberOfSeoPagesAfterCreation = await boSeoUrlsPage.resetAndGetNumberOfLines(page);
      expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1);
    });
  });

  describe('Delete seo page', async () => {
    it('should filter by seo page name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boSeoUrlsPage.filterTable(page, 'page', editSeoPageData.page);

      const numberOfSeoPagesAfterFilter = await boSeoUrlsPage.getNumberOfElementInGrid(page);
      expect(numberOfSeoPagesAfterFilter).to.be.at.least(1);

      const textColumn = await boSeoUrlsPage.getTextColumnFromTable(page, 1, 'page');
      expect(textColumn).to.contains(editSeoPageData.page);
    });

    it('should delete seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteSeoPage', baseContext);

      // delete seo page in first row
      const result = await boSeoUrlsPage.deleteSeoUrlPage(page, 1);
      expect(result).to.be.equal(boSeoUrlsPage.successfulDeleteMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSeoPagesAfterCreation = await boSeoUrlsPage.resetAndGetNumberOfLines(page);
      expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages);
    });
  });
});
