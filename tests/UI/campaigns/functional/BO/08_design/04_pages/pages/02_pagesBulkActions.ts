// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import addPagePage from '@pages/BO/design/pages/add';
import pagesPage from '@pages/BO/design/pages';

// Import data
import CMSPageData from '@data/faker/CMSpage';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_pages_pages_pagesBulkActions';

/*
Create 2 new pages
Enable/Disable/Delete pages by bulk actions
 */
describe('BO - Design - Pages : Enable/Disable/Delete pages with Bulk Actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfPages: number = 0;

  const firstPageData: CMSPageData = new CMSPageData({title: 'todelete'});
  const secondPageData: CMSPageData = new CMSPageData({title: 'todelete'});
  const pagesTable: string = 'cms_page';

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

  it('should go to \'Design > Pages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.pagesLink,
    );
    await pagesPage.closeSfToolBar(page);

    const pageTitle = await pagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(pagesPage.pageTitle);
  });

  it('should reset filter and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfPages = await pagesPage.resetAndGetNumberOfLines(page, pagesTable);
    expect(numberOfPages).to.be.above(0);
  });

  // 1 : Create 2 pages In BO
  describe('Create 2 pages', async () => {
    [firstPageData, secondPageData].forEach((pageToCreate: CMSPageData, index: number) => {
      it('should go to add new page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddPage${index + 1}`, baseContext);

        await pagesPage.goToAddNewPage(page);

        const pageTitle = await addPagePage.getPageTitle(page);
        expect(pageTitle).to.contains(addPagePage.pageTitleCreate);
      });

      it(`should create page n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createPage${index + 1}`, baseContext);

        const textResult = await addPagePage.createEditPage(page, pageToCreate);
        expect(textResult).to.equal(pagesPage.successfulCreationMessage);
      });
    });
  });

  // 2 : Enable/Disable Pages created with bulk actions
  describe('Enable and Disable pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkEditStatus', baseContext);

      await pagesPage.filterTable(page, pagesTable, 'input', 'meta_title', 'todelete');

      const textResult = await pagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textResult).to.contains('todelete');
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((pageStatus) => {
      it(`should ${pageStatus.args.status} pages with Bulk Actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${pageStatus.args.status}Page`, baseContext);

        const textResult = await pagesPage.bulkSetStatus(page, pagesTable, pageStatus.args.enable);
        expect(textResult).to.be.equal(pagesPage.successfulUpdateStatusMessage);

        const numberOfPagesInGrid = await pagesPage.getNumberOfElementInGrid(page, pagesTable);
        expect(numberOfPagesInGrid).to.be.at.most(numberOfPages);

        for (let i = 1; i <= numberOfPagesInGrid; i++) {
          const textColumn = await pagesPage.getStatus(page, pagesTable, i);
          expect(textColumn).to.equal(pageStatus.args.enable);
        }
      });
    });
  });

  // 3 : Delete Pages created with bulk actions
  describe('Delete pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await pagesPage.filterTable(page, pagesTable, 'input', 'meta_title', 'todelete');

      const textResult = await pagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textResult).to.contains('todelete');
    });

    it('should delete pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await pagesPage.deleteWithBulkActions(page, pagesTable);
      expect(deleteTextResult).to.be.equal(pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfPagesAfterFilter = await pagesPage.resetAndGetNumberOfLines(page, pagesTable);
      expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages);
    });
  });
});
