require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const CategoryPageFaker = require('@data/faker/CMScategory');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const AddPageCategoryPage = require('@pages/BO/design/pages/addPageCategory');
const PagesPage = require('@pages/BO/design/pages/pages');

let browser;
let page;
let numberOfCategories = 0;
let createFirstCategoryData;
let createSecondCategoryData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    addPageCategoryPage: new AddPageCategoryPage(page),
    pagesPage: new PagesPage(page),
  };
};

// Filter And Quick Edit Pages
describe('Filter And Quick Edit Categories', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createFirstCategoryData = await (new CategoryPageFaker());
    createSecondCategoryData = await (new CategoryPageFaker());
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

  // 1 : Create two categories and Filter with all inputs and selects in grid table
  describe('Create 2 categories then filter the table', async () => {
    // Create 2 categories
    describe('Create Categories', async () => {
      it('should go to add new page category', async function () {
        await this.pageObjects.pagesPage.goToAddNewPageCategory();
        const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
      });

      it('should create the first category ', async function () {
        const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(createFirstCategoryData);
        await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
      });

      it('should go back to categories list', async function () {
        await this.pageObjects.pagesPage.backToList();
        const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
      });

      it('should go to add new page category', async function () {
        await this.pageObjects.pagesPage.goToAddNewPageCategory();
        const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
      });

      it('should create the second category ', async function () {
        const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(createSecondCategoryData);
        await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
      });

      it('should go back to categories list', async function () {
        await this.pageObjects.pagesPage.backToList();
        const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
      });
    });

    // Filter categories table
    describe('Filter Categories', async () => {
      it('should reset filter and get number of categories in BO', async function () {
        numberOfCategories = await this.pageObjects.pagesPage.resetFilter('cms_page_category');
        await expect(numberOfCategories).to.be.above(0);
      });

      it('should filter by Id \'4\'', async function () {
        await this.pageObjects.pagesPage.filterTable('cms_page_category', 'input', 'id_cms_category', '4');
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page_category'),
        );
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.listTableColumn
            .replace('%TABLE', 'cms_page_category')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'id_cms_category'),
        );
        await expect(textColumn).to.contains('4');
      });

      it('should reset all filters', async function () {
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetFilter('cms_page_category');
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });

      it('should filter by category name', async function () {
        await this.pageObjects.pagesPage.filterTable(
          'cms_page_category',
          'input',
          'name',
          createFirstCategoryData.name);
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page_category'),
        );
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.listTableColumn
            .replace('%TABLE', 'cms_page_category')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'name'),
        );
        await expect(textColumn).to.contains(createFirstCategoryData.name);
      });

      it('should reset all filters', async function () {
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetFilter('cms_page_category');
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });

      it('should filter by Description', async function () {
        await this.pageObjects.pagesPage.filterTable('cms_page_category',
          'input',
          'description',
          createSecondCategoryData.description,
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page_category'),
        );
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.listTableColumn
            .replace('%TABLE', 'cms_page_category')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'description'),
        );
        await expect(textColumn).to.contains(createSecondCategoryData.description);
      });

      it('should reset all filters', async function () {
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetFilter('cms_page_category');
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });

      it('should filter by Position \'1\'', async function () {
        await this.pageObjects.pagesPage.filterTable('cms_page_category', 'input', 'position', '1');
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page_category'),
        );
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.listTableColumn
            .replace('%TABLE', 'cms_page_category')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'position'),
        );
        await expect(textColumn).to.contains('1');
      });

      it('should reset all filters', async function () {
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetFilter('cms_page_category');
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });

      it('should filter by Displayed \'Yes\'', async function () {
        await this.pageObjects.pagesPage.filterTable('cms_page_category',
          'select',
          'active',
          createSecondCategoryData.displayed,
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page_category'),
        );
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        /* eslint-disable no-await-in-loop */
        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextContent(
            this.pageObjects.pagesPage.listTableColumn
              .replace('%TABLE', 'cms_page_category')
              .replace('%ROW', i)
              .replace('%COLUMN', 'active'),
          );
          await expect(textColumn).to.contains('check');
        }
        /* eslint-enable no-await-in-loop */
      });

      it('should reset all filters', async function () {
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetFilter('cms_page_category');
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });
    });
  });
  // 2 : Editing Categories from grid table
  describe('Quick Edit Categories', async () => {
    it('should filter by category name', async function () {
      await this.pageObjects.pagesPage.filterTable('cms_page_category',
        'input',
        'name',
        createFirstCategoryData.name,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.gridTitle.replace('%TABLE', 'cms_page_category'),
      );
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      const textColumn = await this.pageObjects.pagesPage.getTextContent(
        this.pageObjects.pagesPage.listTableColumn
          .replace('%TABLE', 'cms_page_category')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(createFirstCategoryData.name);
    });

    it('should disable the Category', async function () {
      const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValue(
        'cms_page_category',
        '1',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.columnNotValidIcon
          .replace('%TABLE', 'cms_page_category')
          .replace('%ROW', 1),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should enable the Category', async function () {
      const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValue(
        'cms_page_category',
        '1',
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.columnValidIcon
          .replace('%TABLE', 'cms_page_category')
          .replace('%ROW', 1),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.resetFilter('cms_page_category');
      await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
    });

    it('should delete categories with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.pagesPage.deleteRowInTableBulkActions('cms_page_category');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.pagesPage.successfulMultiDeleteMessage);
    });
  });
});
