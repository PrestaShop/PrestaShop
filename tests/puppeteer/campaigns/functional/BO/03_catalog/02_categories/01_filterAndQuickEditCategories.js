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
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_categories_filterAndQuickEditCategories';

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
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.categoriesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });

  it('should reset all filters and get Number of Categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);
    numberOfCategories = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
    await expect(numberOfCategories).to.be.above(0);
  });
  // 1 : Filter Categories with all inputs and selects in grid table
  describe('Filter Categories', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_category',
            filterValue: Categories.art.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Categories.accessories.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterDescription',
            filterType: 'input',
            filterBy: 'description',
            filterValue: Categories.accessories.description,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterPosition',
            filterType: 'input',
            filterBy: 'position',
            filterValue: Categories.art.position,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Categories.accessories.displayed,
          },
        expected: 'check',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);
        await this.pageObjects.categoriesPage.filterCategories(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(
            i,
            test.args.filterBy,
          );
          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);
        const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
        await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
      });
    });
  });
  // 2 : Editing categories from grid table
  describe('Quick Edit Categories', async () => {
    // Steps
    it('should filter by Name \'Art\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        Categories.art.name,
      );
      const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
      await expect(numberOfCategoriesAfterFilter).to.be.at.above(0);
    });
    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];
    tests.forEach((test) => {
      it(`should ${test.args.action} first Category`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.status}Category`, baseContext);
        const isActionPerformed = await this.pageObjects.categoriesPage.updateToggleColumnValue(
          1,
          'active',
          test.args.enabledValue,
        );
        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.categoriesPage.getTextContent(
            this.pageObjects.categoriesPage.growlDefaultMessageBlock,
          );
          await expect(resultMessage).to.contains(this.pageObjects.categoriesPage.successfulUpdateStatusMessage);
        }
        const isStatusChanged = await this.pageObjects.categoriesPage.getToggleColumnValue(1, 'active');
        await expect(isStatusChanged).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
