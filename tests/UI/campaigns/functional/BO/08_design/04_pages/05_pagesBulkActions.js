require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const PageFaker = require('@data/faker/CMSpage');

// Import pages
const dashboardPage = require('@pages/BO/dashboard/index');
const pagesPage = require('@pages/BO/design/pages/index');
const addPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');
const addPagePage = require('@pages/BO/design/pages/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_pagesBulkAction';


let browserContext;
let page;
let numberOfPages = 0;

const firstPageData = new PageFaker({title: 'todelete'});
const secondPageData = new PageFaker({title: 'todelete'});

// Create Pages, Then disable / Enable and Delete with Bulk actions
describe('Create Pages, Then disable / Enable and Delete with Bulk actions', async () => {
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

  // Go to Design>Pages page
  it('should go to "Design>Pages" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.pagesLink,
    );

    await pagesPage.closeSfToolBar(page);

    const pageTitle = await pagesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(pagesPage.pageTitle);
  });

  it('should reset filter and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfPages = await pagesPage.resetAndGetNumberOfLines(page, 'cms_page');
    await expect(numberOfPages).to.be.above(0);
  });

  // 1 : Create 2 pages In BO
  describe('Create 2 pages', async () => {
    const pagesToCreate = [firstPageData, secondPageData];

    pagesToCreate.forEach((pageToCreate, index) => {
      it('should go to add new page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddPage${index + 1}`, baseContext);

        await pagesPage.goToAddNewPage(page);
        const pageTitle = await addPageCategoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addPageCategoryPage.pageTitleCreate);
      });

      it('should create page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createPage${index + 1}`, baseContext);

        const textResult = await addPagePage.createEditPage(page, pageToCreate);
        await expect(textResult).to.equal(pagesPage.successfulCreationMessage);
      });
    });
  });

  // 2 : Enable/Disable Pages created with bulk actions
  describe('Enable and Disable pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkEditStatus', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page',
        'input',
        'meta_title',
        'todelete',
      );

      const textResult = await pagesPage.getTextColumnFromTableCmsPage(
        page,
        1,
        'meta_title',
      );

      await expect(textResult).to.contains('todelete');
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];

    statuses.forEach((pageStatus) => {
      it(`should ${pageStatus.args.status} pages with Bulk Actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${pageStatus.args.status}Page`, baseContext);

        const textResult = await pagesPage.bulkSetStatus(
          page,
          'cms_page',
          pageStatus.args.enable);

        await expect(textResult).to.be.equal(pagesPage.successfulUpdateStatusMessage);

        const numberOfPagesInGrid = await pagesPage.getNumberOfElementInGrid(page, 'cms_page');
        await expect(numberOfPagesInGrid).to.be.at.most(numberOfPages);

        for (let i = 1; i <= numberOfPagesInGrid; i++) {
          const textColumn = await pagesPage.getStatus(page, 'cms_page', i, 'active');
          await expect(textColumn).to.equal(pageStatus.args.enable);
        }
      });
    });
  });

  // 3 : Delete Pages created with bulk actions
  describe('Delete pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page',
        'input',
        'meta_title',
        'todelete',
      );

      const textResult = await pagesPage.getTextColumnFromTableCmsPage(
        page,
        1,
        'meta_title',
      );

      await expect(textResult).to.contains('todelete');
    });

    it('should delete pages with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await pagesPage.deleteWithBulkActions(page, 'cms_page');
      await expect(deleteTextResult).to.be.equal(pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfPagesAfterFilter = await pagesPage.resetAndGetNumberOfLines(page, 'cms_page');
      await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages);
    });
  });
});
