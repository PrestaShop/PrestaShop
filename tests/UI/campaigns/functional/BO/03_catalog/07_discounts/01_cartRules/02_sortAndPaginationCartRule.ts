import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCartRule,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_sortAndPaginationCartRule';

/*
 * Create 21 cart rules
 * Pagination between pages
 * Sort cart rules table by Id, name, priority, code, quantity and date
 * Delete created cart rules by bulk actions
 */
describe('BO - Catalog - Discounts : Sort and pagination cart rules', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCartRules: number = 0;

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

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.discountsLink,
    );

    const pageTitle = await boCartRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
  });

  it('should reset filter and get number of cart rules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCartRules = await boCartRulesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCartRules).to.be.at.least(0);
  });

  // 1 - create 21 cart rules
  describe('Create 21 cart rules', async () => {
    const creationTests: number[] = new Array(21).fill(0, 0, 21);
    creationTests.forEach((test: number, index: number) => {
      const cartRuleData: FakerCartRule = new FakerCartRule({
        name: `todelete${index}`,
        discountType: 'Percent',
        discountPercent: 20,
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewCartRulePage${index}`, baseContext);

        await boCartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
      });

      it(`should create cart rule n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCartRule${index}`, baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, cartRuleData);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);

        const numberOfCartRulesAfterCreation = await boCartRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCartRulesAfterCreation).to.be.equal(numberOfCartRules + 1 + index);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await boCartRulesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boCartRulesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boCartRulesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await boCartRulesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 - Sort table
  describe('Sort cart rules table', async () => {
    const sortTests = [
      {
        testIdentifier: 'sortByIdDesc', sortBy: 'id_cart_rule', sortDirection: 'down', isFloat: true,
      },
      {
        testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'up',
      },
      {
        testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'down',
      },
      {
        testIdentifier: 'sortByPriorityAsc', sortBy: 'priority', sortDirection: 'up', isFloat: true,
      },
      {
        testIdentifier: 'sortByPriorityDesc', sortBy: 'priority', sortDirection: 'down', isFloat: true,
      },
      {
        testIdentifier: 'sortByCodeAsc', sortBy: 'code', sortDirection: 'up',
      },
      {
        testIdentifier: 'sortByCodeDesc', sortBy: 'code', sortDirection: 'down',
      },
      {
        testIdentifier: 'sortByQuantityAsc', sortBy: 'quantity', sortDirection: 'up', isFloat: true,
      },
      {
        testIdentifier: 'sortByQuantityDesc', sortBy: 'quantity', sortDirection: 'down', isFloat: true,
      },
      {
        testIdentifier: 'sortByDateAsc', sortBy: 'date', sortDirection: 'up', isDate: true,
      },
      {
        testIdentifier: 'sortByDateDesc', sortBy: 'date', sortDirection: 'down', isDate: true,
      },
      {
        testIdentifier: 'sortByIdAsc', sortBy: 'id_cart_rule', sortDirection: 'up', isFloat: true,
      },
    ];
    sortTests.forEach((test: {
      testIdentifier: string,
      sortBy: string,
      sortDirection: string,
      isDate?: boolean,
      isFloat?: boolean
    }) => {
      it(`should sort by '${test.sortBy}' '${test.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        const nonSortedTable = await boCartRulesPage.getAllRowsColumnContent(page, test.sortBy);

        await boCartRulesPage.sortTable(page, test.sortBy, test.sortDirection);

        const sortedTable = await boCartRulesPage.getAllRowsColumnContent(page, test.sortBy);

        if (test.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.sortDirection === 'up') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else if (test.isDate) {
          const expectedResult: string[] = await utilsCore.sortArrayDate(nonSortedTable);

          if (test.sortDirection === 'up') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

          if (test.sortDirection === 'up') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 4 : Delete with bulk actions
  describe('Delete cart rules with bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boCartRulesPage.filterCartRules(page, 'input', 'name', 'todelete');

      const numberOfCartRulesAfterFilter = await boCartRulesPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCartRulesAfterFilter; i++) {
        const textColumn = await boCartRulesPage.getTextColumn(page, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete cart rules and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await boCartRulesPage.bulkDeleteCartRules(page);
      expect(deleteTextResult).to.be.contains(boCartRulesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterBulkDelete', baseContext);

      const numberOfCartRulesAfterDelete = await boCartRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCartRulesAfterDelete).to.equal(numberOfCartRules);
    });
  });
});
