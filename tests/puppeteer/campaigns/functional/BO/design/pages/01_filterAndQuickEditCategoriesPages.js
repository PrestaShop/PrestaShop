require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const AddPageCategoryPage = require('@pages/BO/design/pages/addPageCategory');
const PagesPage = require('@pages/BO/design/pages/pages');
const CategoryPageFaker = require('@data/faker/CMSCategory');
const {Pages} = require('@data/demo/CMSpage');

let browser;
let page;
let numberOfPages = 0;
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
describe('Filter And Quick Edit Categories / Pages', async () => {
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
  // Login into BO and go to categories page
  loginCommon.loginBO();
  it('should go to "Design>Pages" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.designParentLink,
      this.pageObjects.boBasePage.pagesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
  });
  // 1 : Filter Categories with all inputs and selects in grid table
  describe('Create 2 categories and filter them', async () => {
    describe('Create 2 categories', async () => {
      it('should go to add new page category', async function () {
        await this.pageObjects.pagesPage.clickAndWaitForNavigation(
          this.pageObjects.pagesPage.addNewPageCategoryLink,
        );
        const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
      });
      it('should create the first category ', async function () {
        const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(createFirstCategoryData);
        await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
      });
      it('should go back to categories list', async function () {
        await this.pageObjects.pagesPage.clickAndWaitForNavigation(
          this.pageObjects.pagesPage.backToListButton,
        );
        const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
      });
      it('should go to add new page category', async function () {
        await this.pageObjects.pagesPage.clickAndWaitForNavigation(
          this.pageObjects.pagesPage.addNewPageCategoryLink,
        );
        const pageTitle = await this.pageObjects.addPageCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addPageCategoryPage.pageTitleCreate);
      });
      it('should create the second category ', async function () {
        const textResult = await this.pageObjects.addPageCategoryPage.createEditPageCategory(createSecondCategoryData);
        await expect(textResult).to.equal(this.pageObjects.pagesPage.successfulCreationMessage);
      });
      it('should go back to categories list', async function () {
        await this.pageObjects.pagesPage.clickAndWaitForNavigation(
          this.pageObjects.pagesPage.backToListButton,
        );
        const pageTitle = await this.pageObjects.pagesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.pagesPage.pageTitle);
      });
    });
    describe('Filter Categories', async () => {
      it('should get number of categories in BO', async function () {
        if (await this.pageObjects.pagesPage.elementVisible(
          this.pageObjects.pagesPage.categoryfilterResetButton, 2000)) {
          await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
        }
        numberOfCategories = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategories).to.be.above(0);
      });
      it('should filter by Id \'1\'', async function () {
        await this.pageObjects.pagesPage.filterPageCategories(
          'input',
          'id_cms_category',
          '1',
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        /* eslint-disable no-await-in-loop */
        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextContent(
            this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace(
              '%COLUMN',
              'id_cms_category',
            ),
          );
          await expect(textColumn).to.contains('1');
        }
        /* eslint-enable no-await-in-loop */
      });
      it('should reset all filters', async function () {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });
      it('should filter by Name', async function () {
        await this.pageObjects.pagesPage.filterPageCategories(
          'input',
          'name',
          createFirstCategoryData.name,
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        /* eslint-disable no-await-in-loop */
        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextContent(
            this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace(
              '%COLUMN',
              'name',
            ),
          );
          await expect(textColumn).to.contains(createFirstCategoryData.name);
        }
        /* eslint-enable no-await-in-loop */
      });
      it('should reset all filters', async function () {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });
      it('should filter by Description', async function () {
        await this.pageObjects.pagesPage.filterPageCategories(
          'input',
          'description',
          createSecondCategoryData.description,
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        /* eslint-disable no-await-in-loop */
        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextContent(
            this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace(
              '%COLUMN',
              'description',
            ),
          );
          await expect(textColumn).to.contains(createSecondCategoryData.description);
        }
        /* eslint-enable no-await-in-loop */
      });
      it('should reset all filters', async function () {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });
      it('should filter by Position \'1\'', async function () {
        await this.pageObjects.pagesPage.filterPageCategories(
          'input',
          'position',
          '1',
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        /* eslint-disable no-await-in-loop */
        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextContent(
            this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace(
              '%COLUMN',
              'position',
            ),
          );
          await expect(textColumn).to.contains('1');
        }
        /* eslint-enable no-await-in-loop */
      });
      it('should reset all filters', async function () {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });
      it('should filter by Displayed \'Yes\'', async function () {
        await this.pageObjects.pagesPage.filterPageCategories(
          'select',
          'active',
          createSecondCategoryData.displayed,
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        /* eslint-disable no-await-in-loop */
        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await this.pageObjects.pagesPage.getTextContent(
            this.pageObjects.pagesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
          );
          await expect(textColumn).to.contains('check');
        }
        /* eslint-enable no-await-in-loop */
      });
      it('should reset all filters', async function () {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.categoryfilterResetButton);
        const numberOfCategoriesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
          this.pageObjects.pagesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.equal(numberOfCategories);
      });
    });
  });
  // 2 : Filter Pages with all inputs and selects in grid table
  describe('Filter Pages', async () => {
    it('should reset all filters and get number of pages in BO', async function () {
      if (await this.pageObjects.pagesPage.elementVisible(this.pageObjects.pagesPage.pagefilterResetButton, 2000)) {
        await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      }
      numberOfPages = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPages).to.be.above(0);
    });
    it('should filter by Id \'1\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'id_cms',
        Pages.delivery.id,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'id_cms'),
        );
        await expect(textColumn).to.contains(Pages.delivery.id);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
    it('should filter by URL \'about-us\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'link_rewrite',
        Pages.aboutUs.url,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'link_rewrite'),
        );
        await expect(textColumn).to.contains(Pages.aboutUs.url);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
    it('should filter by Title \'Terms and conditions of use\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'meta_title',
        Pages.termsAndCondition.title,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'meta_title'),
        );
        await expect(textColumn).to.contains(Pages.termsAndCondition.title);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
    it('should filter by Position \'5\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'position',
        Pages.securePayment.position,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'position'),
        );
        await expect(textColumn).to.contains(Pages.securePayment.position);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
    it('should filter by Displayed \'Yes\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'select',
        'active',
        Pages.securePayment.displayed,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
  });
  // 3 : Editing Categories from grid table
  describe('Quick Edit Categories', async () => {
    // Steps
    it('should filter by Id \'1\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'id_cms',
        Pages.delivery.id,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'id_cms'),
        );
        await expect(textColumn).to.contains(Pages.delivery.id);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should disable the Category', async function () {
      const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValueCategory(
        '1',
        'active',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.alertSuccessBlockParagraph);
        await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.categoriesListColumnNotValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should enable the Category', async function () {
      const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValueCategory(
        '1',
        'active',
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.alertSuccessBlockParagraph);
        await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.categoriesListColumnValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
  });
  // 4 : Editing Pages from grid table
  describe('Quick Edit Pages', async () => {
    // Steps
    it('should filter by Title \'Terms and conditions of use\'', async function () {
      await this.pageObjects.pagesPage.filterPages(
        'input',
        'meta_title',
        Pages.termsAndCondition.title,
      );
      const numberOfPagesAfterFilter = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      if (numberOfPages === 0) {
        await expect(numberOfPagesAfterFilter).to.be.equal(numberOfPages + 1);
      } else await expect(numberOfPagesAfterFilter).to.be.at.most(numberOfPages);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfPagesAfterFilter; i++) {
        const textColumn = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.pagesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'meta_title'),
        );
        await expect(textColumn).to.contains(Pages.termsAndCondition.title);
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should disable the Page', async function () {
      const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValuePage(
        '1',
        'active',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.alertSuccessBlockParagraph);
        await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.pagesListColumnNotValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should enable the Page', async function () {
      const isActionPerformed = await this.pageObjects.pagesPage.updateToggleColumnValuePage(
        '1',
        'active',
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.pagesPage.getTextContent(
          this.pageObjects.pagesPage.alertSuccessBlockParagraph);
        await expect(resultMessage).to.contains(this.pageObjects.pagesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.pagesPage.elementVisible(
        this.pageObjects.pagesPage.pagesListColumnValidIcon
          .replace('%ROW', 1).replace('%COLUMN', 'active'),
        100,
      );
      await expect(isStatusChanged).to.be.true;
    });
    it('should reset all filters', async function () {
      await this.pageObjects.pagesPage.resetFilter(this.pageObjects.pagesPage.pagefilterResetButton);
      const numberOfPagesAfterReset = await this.pageObjects.pagesPage.getNumberFromText(
        this.pageObjects.pagesPage.pageGridTitle);
      await expect(numberOfPagesAfterReset).to.be.equal(numberOfPages);
    });
  });
});
