import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCategoriesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCategories,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_categories_filterAndQuickEditCategories';

// Filter and quick edit Categories
describe('BO - Catalog - Categories : Filter and quick edit Categories table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;

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

  it('should go to \'Catalog > Categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.categoriesLink,
    );
    await boCategoriesPage.closeSfToolBar(page);

    const pageTitle = await boCategoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
  });

  it('should reset all filters and get number of Categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
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
            filterValue: dataCategories.art.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: dataCategories.accessories.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterDescription',
            filterType: 'input',
            filterBy: 'description',
            filterValue: dataCategories.accessories.description,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterPosition',
            filterType: 'input',
            filterBy: 'position',
            filterValue: dataCategories.art.position.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: dataCategories.accessories.displayed ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boCategoriesPage.filterCategories(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        // At least 1 category should be displayed after these filters
        // Can't know most categories that can be displayed
        // because we don't have total of categories and subcategories
        const numberOfCategoriesAfterFilter = await boCategoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterFilter).to.be.at.least(1);

        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const categoryStatus = await boCategoriesPage.getStatus(page, i);
            expect(categoryStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(
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

        const numberOfCategoriesAfterReset = await boCategoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
      });
    });
  });

  // 2 : Editing categories from grid table
  describe('Quick edit Categories', async () => {
    // Steps
    it('should filter by Name \'Art\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await boCategoriesPage.filterCategories(
        page,
        'input',
        'name',
        dataCategories.art.name,
      );

      const numberOfCategoriesAfterFilter = await boCategoriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCategoriesAfterFilter).to.be.at.above(0);
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} first Category`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Category`, baseContext);

        const isActionPerformed = await boCategoriesPage.setStatus(
          page,
          1,
          test.args.enabledValue,
        );

        if (isActionPerformed) {
          const resultMessage = await boCategoriesPage.getGrowlMessageContent(page);

          expect(resultMessage).to.contains(boCategoriesPage.successfulUpdateStatusMessage);
        }

        const categoryStatus = await boCategoriesPage.getStatus(page, 1);
        expect(categoryStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfCategoriesAfterReset = await boCategoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
