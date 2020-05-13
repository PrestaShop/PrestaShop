require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const PageFaker = require('@data/faker/CMSpage');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const PagesPage = require('@pages/BO/design/pages');
const AddPagePage = require('@pages/BO/design/pages/add');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_pagesPagination';

let browser;
let page;
let numberOfPages = 0;
const createPageData = new PageFaker();

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
    addPagePage: new AddPagePage(page),
  };
};

describe('Pages pagination', async () => {
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

  // Go to Design > Pages page
  it('should go to "Design > Pages" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.pagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });

  it('should reset all filters and get number of pages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);
    numberOfPages = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page');
    if (numberOfPages !== 0) await expect(numberOfPages).to.be.above(0);
  });

  // 1 : Create 11 pages
  const tests = new Array(11).fill(0, 0, 11);
  tests.forEach((test, index) => {
    describe(`Create page n°${index + 1} in BO`, async () => {
      it('should go to add new page page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewPagePage${index}`, baseContext);
        await this.pageObjects.pagesPage.goToAddNewPage();
        const pageTitle = await this.pageObjects.addPagePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addPagePage.pageTitleCreate);
      });

      it('should create page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createPage${index}`, baseContext);
        const textResult = await this.pageObjects.addPagePage.createEditPage(createPageData);
        await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
      });

      it('should check the pages number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkPagesNumber${index}`, baseContext);
        const numberOfPagesAfterCreation = await this.pageObjects.pagesPage.getNumberOfElementInGrid('cms_page');
        await expect(numberOfPagesAfterCreation).to.be.equal(numberOfPages + 1 + index);
      });
    });
  });

  // 2 : Test pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);
      const paginationNumber = await this.pageObjects.pagesPage.selectPagesPaginationLimit('10');
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);
      const paginationNumber = await this.pageObjects.pagesPage.paginationPagesNext();
      expect(paginationNumber).to.contain('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);
      const paginationNumber = await this.pageObjects.pagesPage.paginationPagesPrevious();
      expect(paginationNumber).to.contain('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);
      const paginationNumber = await this.pageObjects.pagesPage.selectPagesPaginationLimit('50');
      expect(paginationNumber).to.contain('(page 1 / 1)');
    });
  });
  // 3 : Delete the 11 pages with bulk actions
  describe('Delete pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'meta_title',
        createPageData.title,
      );
      const textResult = await this.pageObjects.pagesPage.getTextColumnFromTableCmsPage(1, 'meta_title');
      await expect(textResult).to.contains(createPageData.title);
    });

    it('should delete pages with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);
      const deleteTextResult = await this.pageObjects.pagesPage.deleteWithBulkActions('cms_page');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.resetAndGetNumberOfLines('cms_page');
      await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages);
    });
  });
});
