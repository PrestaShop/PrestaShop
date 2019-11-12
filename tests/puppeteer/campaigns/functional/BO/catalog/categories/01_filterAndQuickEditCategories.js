require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Categories} = require('@data/demo/categories');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CategoriesPage = require('@pages/BO/catalog/categories');

let browser;
let page;
let numberOfCategories = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    categoriesPage: new CategoriesPage(page),
  };
};

// Filter And Quick Edit Categories
describe('Filter And Quick Edit Categories', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to categories page
  loginCommon.loginBO();

  it('should go to "Catalog>Categories" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.categoriesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });

  it('should reset all filters and get Number of Categories in BO', async function () {
    numberOfCategories = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
    await expect(numberOfCategories).to.be.above(0);
  });
  // 1 : Filter Categories with all inputs and selects in grid table
  describe('Filter Categories', async () => {
    it('should filter by Id \'9\'', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'id_category',
        Categories.art.id,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(i, 'id_category');
        await expect(textColumn).to.contains(Categories.art.id);
      }
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });

    it('should filter by Name \'Accessories\'', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        Categories.accessories.name,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(i, 'name');
        await expect(textColumn).to.contains(Categories.accessories.name);
      }
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });

    it('should filter by Description', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'description',
        Categories.accessories.description,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(i, 'description');
        await expect(textColumn).to.contains(Categories.accessories.description);
      }
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });

    it('should filter by Position \'3\'', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'position',
        Categories.art.position,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(i, 'position');
        await expect(textColumn).to.contains(Categories.art.position);
      }
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });

    it('should filter by Displayed \'Yes\'', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'select',
        'active',
        Categories.art.displayed,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
      await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
      for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(i, 'active');
        await expect(textColumn).to.contains('check');
      }
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
  // 2 : Editing categories from grid table
  describe('Quick Edit Categories', async () => {
    // Steps
    it('should filter by Name \'Art\'', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        Categories.art.name,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
      await expect(numberOfCategoriesAfterFilter).to.be.at.above(0);
    });

    it('should disable the Category', async function () {
      const isActionPerformed = await this.pageObjects.categoriesPage.updateToggleColumnValue(
        '1',
        'active',
        false,
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.growlDefaultMessageBloc);
        await expect(resultMessage).to.contains(this.pageObjects.categoriesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.categoriesPage.getToggleColumnValue(1, 'active');
      await expect(isStatusChanged).to.be.false;
    });

    it('should enable the Category', async function () {
      const isActionPerformed = await this.pageObjects.categoriesPage.updateToggleColumnValue(
        '1',
        'active',
      );
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.growlDefaultMessageBloc);
        await expect(resultMessage).to.contains(this.pageObjects.categoriesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.categoriesPage.getToggleColumnValue(1, 'active');
      await expect(isStatusChanged).to.be.true;
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
