// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import seoAndUrlsPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls';
import addSeoAndUrlPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls/add';

// Import data
import SeoPages from '@data/demo/seoPages';
import SeoPageData from '@data/faker/seoPage';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_seoAndUrls_bulkDeleteSeoPages';

describe('BO - Shop Parameters - Traffic & SEO : Bulk delete seo pages', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSeoPages: number = 0;

  const seoPagesData: SeoPageData[] = [
    new SeoPageData({page: SeoPages.orderReturn.page, title: 'ToDelete1'}),
    new SeoPageData({page: SeoPages.pdfOrderReturn.page, title: 'ToDelete2'}),
  ];

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

  it('should go to \'Shop Parameters > SEO and Urls\' page', async function () {
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

  describe('Create 2 seo pages', async () => {
    seoPagesData.forEach((seoPageData: SeoPageData, index: number) => {
      it('should go to new seo page page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewSeoPage${index + 1}`, baseContext);

        await seoAndUrlsPage.goToNewSeoUrlPage(page);

        const pageTitle = await addSeoAndUrlPage.getPageTitle(page);
        expect(pageTitle).to.contains(addSeoAndUrlPage.pageTitle);
      });

      it('should create seo page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createSeoPage${index + 1}`, baseContext);

        const result = await addSeoAndUrlPage.createEditSeoPage(page, seoPageData);
        expect(result).to.equal(seoAndUrlsPage.successfulCreationMessage);

        const numberOfSeoPagesAfterCreation = await seoAndUrlsPage.getNumberOfElementInGrid(page);
        expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1 + index);
      });
    });
  });

  describe('Delete seo pages by bulk actions', async () => {
    it('should filter by seo page name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await seoAndUrlsPage.filterTable(page, 'title', 'toDelete');

      const textColumn = await seoAndUrlsPage.getTextColumnFromTable(page, 1, 'title');
      expect(textColumn).to.contains('ToDelete');
    });

    it('should bulk delete seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteSeoPage', baseContext);

      // Delete seo page in first row
      const result = await seoAndUrlsPage.bulkDeleteSeoUrlPage(page);
      expect(result).to.be.equal(seoAndUrlsPage.successfulMultiDeleteMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSeoPagesAfterCreation = await seoAndUrlsPage.resetAndGetNumberOfLines(page);
      expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages);
    });
  });
});
