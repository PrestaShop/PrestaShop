require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const catalogPriceRulesPage = require('@pages/BO/catalog/discounts/catalogPriceRules');
const addCatalogPriceRulePage = require('@pages/BO/catalog/discounts/catalogPriceRules/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_discounts_catalogPriceRules_filterSortAndPagination';

// Import expect from chai
const {expect} = require('chai');

// Import data
const PriceRuleFaker = require('@data/faker/catalogPriceRule');

// Browser and tab
let browserContext;
let page;

let numberOfCatalogPriceRules = 0;

// Today date
const today = new Date();

// Current day
const day = (`0${today.getDate()}`).slice(-2);

// Current month
const month = (`0${today.getMonth() + 1}`).slice(-2);

// Current year
const year = today.getFullYear();

// Date today format (yyyy-mm-dd)
const todayDate = `${year}-${month}-${day}`;

// Date today format (mm/dd/yyyy)
const todayDateToCheck = `${month}/${day}/${year}`;

const priceRuleData = new PriceRuleFaker({fromDate: todayDate, toDate: todayDate});
/*
Create 21 catalog price rules
Filter catalog price rules by id, Name, Shop, Currency, Country, Group, From quantity, Reduction type,
Reduction, Beginning, End
Sort catalog price rules by id, Name, Shop, Currency, Country, Group, From quantity, Reduction type,
Reduction, Beginning, End
Pagination next and previous
Delete created catalog price rules by bulk actions
 */
describe('BO - Catalog - Discounts : Filter, sort and pagination catalog price rules table', async () => {
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
  });

  it('should go to \'Catalog Price Rules\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

    await cartRulesPage.goToCatalogPriceRulesTab(page);

    numberOfCatalogPriceRules = await catalogPriceRulesPage.resetAndGetNumberOfLines(page);

    const pageTitle = await catalogPriceRulesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(catalogPriceRulesPage.pageTitle);
  });

  // 1 - Create 21 catalog price rules
  describe('Create 21 catalog price rules in BO', async () => {
    const creationTests = new Array(21).fill(0, 0, 21);
    creationTests.forEach((test, index) => {
      const priceRuleData = new PriceRuleFaker({name: `todelete${index}`, fromDate: todayDate, toDate: todayDate});

      it('should go to new catalog price rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewCatalogPriceRule${index}`, baseContext);

        await catalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

        const pageTitle = await addCatalogPriceRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCatalogPriceRulePage.pageTitle);
      });

      it(`should create catalog price rule n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCatalogPriceRule${index}`, baseContext);

        const validationMessage = await addCatalogPriceRulePage.setCatalogPriceRule(page, priceRuleData);
        await expect(validationMessage).to.contains(catalogPriceRulesPage.successfulCreationMessage);

        const numberOfCatalogPriceRulesAfterCreation = await catalogPriceRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCatalogPriceRulesAfterCreation).to.be.at.most(numberOfCatalogPriceRules + index + 1);
      });
    });
  });

  // 2 - Filter catalog price rules table
  describe('Filter catalog price rules table', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterID', filterType: 'input', filterBy: 'id_specific_price_rule', filterValue: 1,
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'a!name', filterValue: priceRuleData.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterShop', filterType: 'input', filterBy: 's!name', filterValue: global.INSTALL.SHOP_NAME,
        },
      },
      {
        args: {
          testIdentifier: 'filterCurrency',
          filterType: 'input',
          filterBy: 'cul!name',
          filterValue: priceRuleData.currency,
        },
      },
      {
        args: {
          testIdentifier: 'filterCountry',
          filterType: 'input',
          filterBy: 'cl!name',
          filterValue: priceRuleData.country,
        },
      },
      {
        args: {
          testIdentifier: 'filterGroup', filterType: 'input', filterBy: 'gl!name', filterValue: priceRuleData.group,
        },
      },
      {
        args: {
          testIdentifier: 'filterFromQuantity',
          filterType: 'input',
          filterBy: 'from_quantity',
          filterValue: priceRuleData.fromQuantity,
        },
      },
      {
        args: {
          testIdentifier: 'filterReductionType',
          filterType: 'select',
          filterBy: 'a!reduction_type',
          filterValue: priceRuleData.reductionType,
        },
      },
      {
        args: {
          testIdentifier: 'filterReduction',
          filterType: 'input',
          filterBy: 'reduction',
          filterValue: priceRuleData.reduction,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await catalogPriceRulesPage.filterPriceRules(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfPriceRulesAfterFilter = await catalogPriceRulesPage.getNumberOfElementInGrid(page);
        await expect(numberOfPriceRulesAfterFilter).to.be.at.most(numberOfCatalogPriceRules + 21);

        for (let row = 1; row <= numberOfPriceRulesAfterFilter; row++) {
          const textColumn = await catalogPriceRulesPage.getTextColumn(page, row, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfPriceRulesAfterReset = await catalogPriceRulesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfPriceRulesAfterReset).to.equal(numberOfCatalogPriceRules + 21);
      });
    });

    const filterByDate = [
      {
        args: {
          testIdentifier: 'filterDateBeginning', filterBy: 'from', firstDate: todayDate, secondDate: todayDate,
        },
      },
      {
        args: {
          testIdentifier: 'filterDateEnd', filterBy: 'to', firstDate: todayDate, secondDate: todayDate,
        },
      },
    ];
    filterByDate.forEach((test) => {
      it('should filter by date Beginning and date End', async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        // Filter by date
        await catalogPriceRulesPage.filterByDate(page, test.args.filterBy, test.args.firstDate, test.args.secondDate);

        // Get number of elements
        const numberOfShoppingCartsAfterFilter = await catalogPriceRulesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
          const textColumn = await catalogPriceRulesPage.getTextColumn(
            page,
            row,
            test.args.filterBy,
          );
          await expect(textColumn).to.contains(todayDateToCheck);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfPriceRulesAfterReset = await catalogPriceRulesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfPriceRulesAfterReset).to.equal(numberOfCatalogPriceRules + 21);
      });
    });
  });

  // 3 - Sort Price rules table
  describe('Sort catalog price rules table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_specific_price_rule', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'a!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'a!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByShopAsc', sortBy: 's!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByShopDesc', sortBy: 's!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCurrencyAsc', sortBy: 'cul!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCurrencyDesc', sortBy: 'cul!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryAsc', sortBy: 'cl!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByCountryDesc', sortBy: 'cl!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByGroupAsc', sortBy: 'gl!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByGroupDesc', sortBy: 'gl!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByFromQuantityAsc', sortBy: 'from_quantity', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByFromQuantityDesc', sortBy: 'from_quantity', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByReducingTypeAsc', sortBy: 'a!reduction_type', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByReductionTypeDesc', sortBy: 'a!reduction_type', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByReductionAsc', sortBy: 'reduction', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByReductionDesc', sortBy: 'reduction', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateFromAsc', sortBy: 'from', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateFromDesc', sortBy: 'from', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateToAsc', sortBy: 'to', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByDateToDesc', sortBy: 'to', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_specific_price_rule', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await catalogPriceRulesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await catalogPriceRulesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await catalogPriceRulesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await catalogPriceRulesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await catalogPriceRulesPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await catalogPriceRulesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await catalogPriceRulesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await catalogPriceRulesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 5 - Delete catalog price rules with bulk actions
  describe('Bulk delete catalog price rules', async () => {
    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeletePriceRules', baseContext);

      const deleteTextResult = await catalogPriceRulesPage.bulkDeletePriceRules(page);
      await expect(deleteTextResult).to.be.contains(catalogPriceRulesPage.successfulMultiDeleteMessage);
    });
  });
});
