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
const PagesPage = require('@pages/BO/design/pages/pages');
const AddPageCategoryPage = require('@pages/BO/design/pages/addPageCategory');
const AddPagePage = require('@pages/BO/design/pages/addPage');

let browser;
let page;
let numberOfPages = 0;
let firstPageData;
let secondPageData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    pagesPage: new PagesPage(page),
    addPageCategoryPage: new AddPageCategoryPage(page),
    addPagePage: new AddPagePage(page),
  };
};

// Create Pages, Then disable / Enable and Delete with Bulk actions
describe('Create Pages, Then disable / Enable and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    firstPageData = await (new PageFaker({title: 'todelete'}));
    secondPageData = await (new PageFaker({title: 'todelete'}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO
  loginCommon.loginBO();

  // Go to Design>Pages page
  it('should go to "Design>Pages" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.pagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });


  it('should reset filter and get number of pages in BO', async function () {
    numberOfPages = await this.pageObjects.pagesPage.resetFilter('cms_page');
    await expect(numberOfPages).to.be.above(0);
  });

  // 1 : Create 2 pages In BO
  describe('Create 2 pages', async () => {
    it('should go to add new page', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.addNewPageLink,
      );
      const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
    });

    it('should create the first page ', async function () {
      const textResult = await this.pageObjects.addPagePage.createEditPage(firstPageData);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
    });

    it('should go to add new page page', async function () {
      await this.pageObjects.pagesPage.clickAndWaitForNavigation(
        this.pageObjects.pagesPage.addNewPageLink,
      );
      const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
    });

    it('should create the first page ', async function () {
      const textResult = await this.pageObjects.addPagePage.createEditPage(secondPageData);
      await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
    });
  });
  // 2 : Enable/Disable Pages created with bulk actions
  describe('Enable and Disable pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'meta_title',
        'todelete',
      );
      const textResult = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.listTableColumn.replace('%TABLE', 'cms_page')
          .replace('%ROW', '1').replace('%COLUMN', 'meta_title'),
      );
      await expect(textResult).to.contains('todelete');
    });

    it('should disable pages with Bulk Actions and check Result', async function () {
      const disableTextResult = await this.pageObjects.pagesPage.changeEnabledColumnBulkActions('cms_page', false);
      await expect(disableTextResult).to.be.equal(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      const numberOfPagesInGrid = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page'),
      );
      await expect(numberOfPagesInGrid).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesInGrid; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.listTableColumn.replace('%TABLE', 'cms_page').replace('%ROW', i)
            .replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('clear');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should enable pages with Bulk Actions and check Result', async function () {
      const enableTextResult = await this.pageObjects.pagesPage.changeEnabledColumnBulkActions('cms_page', true);
      await expect(enableTextResult).to.be.equal(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      const numberOfPagesInGrid = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page'),
      );
      await expect(numberOfPagesInGrid).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesInGrid; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.listTableColumn.replace('%TABLE', 'cms_page')
            .replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
  });
  // 3 : Delete Pages created with bulk actions
  describe('Delete pages with Bulk Actions', async () => {
    it('should filter list by Title', async function () {
      await this.pageObjects.pagesPage.filterTable(
        'cms_page',
        'input',
        'meta_title',
        'todelete',
      );
      const textResult = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.listTableColumn.replace('%TABLE', 'cms_page')
          .replace('%ROW', '1').replace('%COLUMN', 'meta_title'),
      );
      await expect(textResult).to.contains('todelete');
    });

    it('should delete pages with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.pagesPage.deleteRowInTableBulkActions('cms_page');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.resetFilter('cms_page');
      await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages);
    });
  });
});
