import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCMSPagesPage,
  boDashboardPage,
  boCMSPagesCreatePage,
  boLoginPage,
  type BrowserContext,
  FakerCMSPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_pages_pages_pagesBulkActions';

/*
Create 2 new pages
Enable/Disable/Delete pages by bulk actions
 */
describe('BO - Design - Pages : Enable/Disable/Delete pages with Bulk Actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfPages: number = 0;

  const firstPageData: FakerCMSPage = new FakerCMSPage({title: 'todelete'});
  const secondPageData: FakerCMSPage = new FakerCMSPage({title: 'todelete'});
  const pagesTable: string = 'cms_page';

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

  it('should go to \'Design > Pages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.pagesLink,
    );
    await boCMSPagesPage.closeSfToolBar(page);

    const pageTitle = await boCMSPagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
  });

  it('should reset filter and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfPages = await boCMSPagesPage.resetAndGetNumberOfLines(page, pagesTable);
    expect(numberOfPages).to.be.above(0);
  });

  // 1 : Create 2 pages In BO
  describe('Create 2 pages', async () => {
    [firstPageData, secondPageData].forEach((pageToCreate: FakerCMSPage, index: number) => {
      it('should go to add new page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddPage${index + 1}`, baseContext);

        await boCMSPagesPage.goToAddNewPage(page);

        const pageTitle = await boCMSPagesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCMSPagesCreatePage.pageTitleCreate);
      });

      it(`should create page n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createPage${index + 1}`, baseContext);

        const textResult = await boCMSPagesCreatePage.createEditPage(page, pageToCreate);
        expect(textResult).to.equal(boCMSPagesPage.successfulCreationMessage);
      });
    });
  });

  // 2 : Enable/Disable Pages created with bulk actions
  describe('Enable and Disable pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkEditStatus', baseContext);

      await boCMSPagesPage.filterTable(page, pagesTable, 'input', 'meta_title', 'todelete');

      const textResult = await boCMSPagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textResult).to.contains('todelete');
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((pageStatus) => {
      it(`should ${pageStatus.args.status} pages with Bulk Actions and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${pageStatus.args.status}Page`, baseContext);

        const textResult = await boCMSPagesPage.bulkSetStatus(page, pagesTable, pageStatus.args.enable);
        expect(textResult).to.be.equal(boCMSPagesPage.successfulUpdateStatusMessage);

        const numberOfPagesInGrid = await boCMSPagesPage.getNumberOfElementInGrid(page, pagesTable);
        expect(numberOfPagesInGrid).to.be.at.most(numberOfPages);

        for (let i = 1; i <= numberOfPagesInGrid; i++) {
          const textColumn = await boCMSPagesPage.getStatus(page, pagesTable, i);
          expect(textColumn).to.equal(pageStatus.args.enable);
        }
      });
    });
  });

  // 3 : Delete Pages created with bulk actions
  describe('Delete pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boCMSPagesPage.filterTable(page, pagesTable, 'input', 'meta_title', 'todelete');

      const textResult = await boCMSPagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textResult).to.contains('todelete');
    });

    it('should delete pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await boCMSPagesPage.deleteWithBulkActions(page, pagesTable);
      expect(deleteTextResult).to.be.equal(boCMSPagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfPagesAfterFilter = await boCMSPagesPage.resetAndGetNumberOfLines(page, pagesTable);
      expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages);
    });
  });
});
