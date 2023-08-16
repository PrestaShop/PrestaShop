// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import categoriesPage from '@pages/BO/catalog/categories';

// Import data
import Categories from '@data/demo/categories';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_categories_filterAndQuickEditCategories';

// Filter and quick edit Categories
describe('BO - Catalog - Categories : Filter and quick edit Categories table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;

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

  it('should go to \'Catalog > Categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.categoriesLink,
    );
    await categoriesPage.closeSfToolBar(page);

    const pageTitle = await categoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of Categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Filter Categories with all inputs and selects in grid table
  describe('Filter Categories table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_category',
            filterValue: Categories.art.id.toString(),
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
            filterValue: Categories.art.position.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Categories.accessories.displayed ? '1' : '0',
          },
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

        // At least 1 category should be displayed after these filters
        // Can't know most categories that can be displayed
        // because we don't have total of categories and subcategories
        const numberOfCategoriesAfterFilter = await categoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterFilter).to.be.at.least(1);

        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const categoryStatus = await categoriesPage.getStatus(page, i);
            expect(categoryStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await categoriesPage.getTextColumnFromTableCategories(
              page,
              i,
              test.args.filterBy,
            );
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
      });
    });
  });

  // 2 : Editing categories from grid table
  describe('Quick edit Categories', async () => {
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
      expect(numberOfCategoriesAfterFilter).to.be.at.above(0);
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} first Category`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Category`, baseContext);

        const isActionPerformed = await categoriesPage.setStatus(
          page,
          1,
          test.args.enabledValue,
        );

        if (isActionPerformed) {
          const resultMessage = await categoriesPage.getGrowlMessageContent(page);

          expect(resultMessage).to.contains(categoriesPage.successfulUpdateStatusMessage);
        }

        const categoryStatus = await categoriesPage.getStatus(page, 1);
        expect(categoryStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
