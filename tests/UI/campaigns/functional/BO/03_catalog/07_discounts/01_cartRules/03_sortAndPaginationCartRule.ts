// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_sortAndPaginationCartRule';

/*
Create 21 cart rules
Pagination between pages
Sort cart rules table by Id, name, priority, code, quantity and date
Delete created cart rules by bulk actions
 */
describe('BO - Catalog - Discounts : Sort and pagination cart rules', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCartRules: number = 0;

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

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.discountsLink,
    );

    const pageTitle = await cartRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(cartRulesPage.pageTitle);
  });

  it('should reset filter and get number of cart rules', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCartRules = await cartRulesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCartRules).to.be.at.least(0);
  });

  // 1 - create 21 cart rules
  describe('Create 21 cart rules', async () => {
    const creationTests: number[] = new Array(21).fill(0, 0, 21);
    creationTests.forEach((test: number, index: number) => {
      const cartRuleData: CartRuleData = new CartRuleData({
        name: `todelete${index}`,
        discountType: 'Percent',
        discountPercent: 20,
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewCartRulePage${index}`, baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await addCartRulePage.getPageTitle(page);
        expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it(`should create cart rule n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCartRule${index}`, baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleData);
        expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);

        const numberOfCartRulesAfterCreation = await cartRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCartRulesAfterCreation).to.be.equal(numberOfCartRules + 1 + index);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await cartRulesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await cartRulesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await cartRulesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await cartRulesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 - Sort table
  describe('Sort cart rules table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_cart_rule', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPriorityAsc', sortBy: 'priority', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByPriorityDesc', sortBy: 'priority', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByCodeAsc', sortBy: 'code', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCodeDesc', sortBy: 'code', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByQuantityAsc', sortBy: 'quantity', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByQuantityDesc', sortBy: 'quantity', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateAsc', sortBy: 'date', sortDirection: 'up', isDate: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateDesc', sortBy: 'date', sortDirection: 'down', isDate: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_cart_rule', sortDirection: 'up', isFloat: true,
        },
      },
    ];
    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await cartRulesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await cartRulesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await cartRulesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'up') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else if (test.args.isDate) {
          const expectedResult: string[] = await basicHelper.sortArrayDate(nonSortedTable);

          if (test.args.sortDirection === 'up') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'up') {
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

      await cartRulesPage.filterCartRules(page, 'input', 'name', 'todelete');

      const numberOfCartRulesAfterFilter = await cartRulesPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCartRulesAfterFilter; i++) {
        const textColumn = await cartRulesPage.getTextColumn(page, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete cart rules and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
      expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterBulkDelete', baseContext);

      const numberOfCartRulesAfterDelete = await cartRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCartRulesAfterDelete).to.equal(numberOfCartRules);
    });
  });
});
