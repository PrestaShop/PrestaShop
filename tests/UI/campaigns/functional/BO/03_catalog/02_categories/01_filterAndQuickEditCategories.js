require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const {Categories} = require('@data/demo/categories');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const categoriesPage = require('@pages/BO/catalog/categories');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_categories_filterAndQuickEditCategories';


let browserContext;
let page;
let numberOfCategories = 0;

// Filter And Quick Edit Categories
describe('Filter And Quick Edit Categories', async () => {
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

  it('should go to "Catalog>Categories" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.categoriesLink,
    );

    await categoriesPage.closeSfToolBar(page);

    const pageTitle = await categoriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should reset all filters and get Number of Categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
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

        await categoriesPage.filterCategories(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCategoriesAfterFilter = await categoriesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);

        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await categoriesPage.getTextColumnFromTableCategories(
            page,
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

        const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
      });
    });
  });

  // 2 : Editing categories from grid table
  describe('Quick Edit Categories', async () => {
    // Steps
    it('should filter by Name \'Art\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        Categories.art.name,
      );

      const numberOfCategoriesAfterFilter = await categoriesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCategoriesAfterFilter).to.be.at.above(0);
    });
    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first Category`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Category`, baseContext);

        const isActionPerformed = await categoriesPage.updateToggleColumnValue(
          page,
          1,
          'active',
          test.args.enabledValue,
        );

        if (isActionPerformed) {
          const resultMessage = await categoriesPage.getTextContent(
            page,
            categoriesPage.growlMessageBlock,
          );

          await expect(resultMessage).to.contains(categoriesPage.successfulUpdateStatusMessage);
        }

        const categoryStatus = await categoriesPage.getToggleColumnValue(page, 1, 'active');
        await expect(categoryStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
