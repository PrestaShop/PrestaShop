require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import data
const CartRuleFaker = require('@data/faker/cartRule');

// import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_cartRules_sortAndPaginationCartRule';

let browserContext;
let page;

let numberOfCartRules = 0;

describe('Sort and pagination cart rules', async () => {
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
    await expect(pageTitle).to.contains(cartRulesPage.pageTitle);

    numberOfCartRules = await cartRulesPage.getNumberOfElementInGrid(page);
  });

  // 1 - create 21 cart rules
  const creationTests = new Array(21).fill(0, 0, 21);

  creationTests.forEach((test, index) => {
    describe(`Create cart rule n°${index + 1}`, async () => {
      const cartRuleData = new CartRuleFaker({
        name: `todelete${index}`,
        discountType: 'Percent',
        discountPercent: 20,
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewCartRulePage${index}`, baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);
        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create new cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCartRule${index}`, baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, cartRuleData);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);

        const numberOfCartRulesAfterCreation = await cartRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCartRulesAfterCreation).to.be.equal(numberOfCartRules + 1 + index);
      });
    });
  });

  // 2 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await cartRulesPage.selectPaginationLimit(page, '20');
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

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await cartRulesPage.selectPaginationLimit(page, '50');
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
          testIdentifier: 'sortByDateAsc', sortBy: 'date', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateDesc', sortBy: 'date', sortDirection: 'down',
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

        let nonSortedTable = await cartRulesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await cartRulesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await cartRulesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await cartRulesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete with bulk actions
  describe('Delete carriers with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await cartRulesPage.filterCartRules(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfCartRulesAfterFilter = await cartRulesPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCartRulesAfterFilter; i++) {
        const textColumn = await cartRulesPage.getTextColumn(
          page,
          i,
          'name',
        );

        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete cart rules with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
      await expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
    });
  });
});
