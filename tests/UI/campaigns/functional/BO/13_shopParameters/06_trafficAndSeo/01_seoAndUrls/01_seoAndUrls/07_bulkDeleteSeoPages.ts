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

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_seoAndUrls_bulkDeleteSeoPages';

describe('BO - Shop Parameters - Traffic & SEO : Bulk delete seo pages', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSeoPages: number = 0;

  const seoPagesData: FakerSeoPage[] = [
    new FakerSeoPage({page: dataSeoPages.orderReturn.page, title: 'ToDelete1'}),
    new FakerSeoPage({page: dataSeoPages.pdfOrderReturn.page, title: 'ToDelete2'}),
  ];

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

  it('should go to \'Shop Parameters > SEO and Urls\' page', async function () {
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

  describe('Create 2 seo pages', async () => {
    seoPagesData.forEach((seoPageData: FakerSeoPage, index: number) => {
      it('should go to new seo page page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewSeoPage${index + 1}`, baseContext);

        await boSeoUrlsPage.goToNewSeoUrlPage(page);

        const pageTitle = await boSeoUrlsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boSeoUrlsCreatePage.pageTitle);
      });

      it('should create seo page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createSeoPage${index + 1}`, baseContext);

        const result = await boSeoUrlsCreatePage.createEditSeoPage(page, seoPageData);
        expect(result).to.equal(boSeoUrlsPage.successfulCreationMessage);

        const numberOfSeoPagesAfterCreation = await boSeoUrlsPage.getNumberOfElementInGrid(page);
        expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages + 1 + index);
      });
    });
  });

  describe('Delete seo pages by bulk actions', async () => {
    it('should filter by seo page name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boSeoUrlsPage.filterTable(page, 'title', 'toDelete');

      const textColumn = await boSeoUrlsPage.getTextColumnFromTable(page, 1, 'title');
      expect(textColumn).to.contains('ToDelete');
    });

    it('should bulk delete seo page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteSeoPage', baseContext);

      // Delete seo page in first row
      const result = await boSeoUrlsPage.bulkDeleteSeoUrlPage(page);
      expect(result).to.be.equal(boSeoUrlsPage.successfulMultiDeleteMessage);
    });

    it('should reset filter and check number of seo pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfSeoPagesAfterCreation = await boSeoUrlsPage.resetAndGetNumberOfLines(page);
      expect(numberOfSeoPagesAfterCreation).to.equal(numberOfSeoPages);
    });
  });
});
