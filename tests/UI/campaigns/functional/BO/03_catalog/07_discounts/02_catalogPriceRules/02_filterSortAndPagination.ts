import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCatalogPriceRulesPage,
  boCatalogPriceRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCatalogPriceRule,
  type Page,
  utilsCore,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_catalogPriceRules_filterSortAndPagination';

/*
 * Create 21 catalog price rules
 * Filter catalog price rules by id, Name, Shop, Currency, Country, Group, From quantity, Reduction type,
 * Reduction, Beginning, End
 * Sort catalog price rules by id, Name, Shop, Currency, Country, Group, From quantity, Reduction type,
 * Reduction, Beginning, End
 * Pagination next and previous
 * Delete created catalog price rules by bulk actions
 */
describe('BO - Catalog - Discounts : Filter, sort and pagination catalog price rules table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCatalogPriceRules: number = 0;

  const today: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const dateToCheck: string = utilsDate.getDateFormat('mm/dd/yyyy');
  const priceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({fromDate: today, toDate: today});

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

  it('should go to \'Catalog Price Rules\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPriceRulesTab', baseContext);

    await boCartRulesPage.goToCatalogPriceRulesTab(page);

    numberOfCatalogPriceRules = await boCatalogPriceRulesPage.resetAndGetNumberOfLines(page);

    const pageTitle = await boCatalogPriceRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCatalogPriceRulesPage.pageTitle);
  });

  // 1 - Create 21 catalog price rules
  describe('Create 21 catalog price rules in BO', async () => {
    const creationTests: number[] = new Array(21).fill(0, 0, 21);
    creationTests.forEach((test: number, index: number) => {
      const priceRuleData: FakerCatalogPriceRule = new FakerCatalogPriceRule({
        name: `todelete${index}`,
        fromDate: today,
        toDate: today,
      });

      it('should go to new catalog price rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewCatalogPriceRule${index}`, baseContext);

        await boCatalogPriceRulesPage.goToAddNewCatalogPriceRulePage(page);

        const pageTitle = await boCatalogPriceRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCatalogPriceRulesCreatePage.pageTitle);
      });

      it(`should create catalog price rule n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCatalogPriceRule${index}`, baseContext);

        const validationMessage = await boCatalogPriceRulesCreatePage.setCatalogPriceRule(page, priceRuleData);
        expect(validationMessage).to.contains(boCatalogPriceRulesPage.successfulCreationMessage);

        const numberOfCatalogPriceRulesAfterCreation = await boCatalogPriceRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCatalogPriceRulesAfterCreation).to.be.at.most(numberOfCatalogPriceRules + index + 1);
      });
    });
  });

  // 2 - Filter catalog price rules table
  describe('Filter catalog price rules table', async () => {
    [
      {
        testIdentifier: 'filterID', filterType: 'input', filterBy: 'id_specific_price_rule', filterValue: '1',
      },
      {
        testIdentifier: 'filterName', filterType: 'input', filterBy: 'a!name', filterValue: priceRuleData.name,
      },
      {
        testIdentifier: 'filterCurrency',
        filterType: 'input',
        filterBy: 'cul!name',
        filterValue: priceRuleData.currency,
      },
      {
        testIdentifier: 'filterCountry',
        filterType: 'input',
        filterBy: 'cl!name',
        filterValue: priceRuleData.country,
      },
      {
        testIdentifier: 'filterGroup', filterType: 'input', filterBy: 'gl!name', filterValue: priceRuleData.group,
      },
      {
        testIdentifier: 'filterFromQuantity',
        filterType: 'input',
        filterBy: 'from_quantity',
        filterValue: priceRuleData.fromQuantity.toString(),
      },
      {
        testIdentifier: 'filterReductionType',
        filterType: 'select',
        filterBy: 'a!reduction_type',
        filterValue: priceRuleData.reductionType,
      },
      {
        testIdentifier: 'filterReduction',
        filterType: 'input',
        filterBy: 'reduction',
        filterValue: priceRuleData.reduction.toString(),
      },
    ].forEach((test: {
      testIdentifier: string
      filterType: string
      filterBy: string
      filterValue: string
    }) => {
      it(`should filter by ${test.filterBy} '${test.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        await boCatalogPriceRulesPage.filterPriceRules(
          page,
          test.filterType,
          test.filterBy,
          test.filterValue,
        );

        const numberOfPriceRulesAfterFilter = await boCatalogPriceRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfPriceRulesAfterFilter).to.be.at.most(numberOfCatalogPriceRules + 21);

        for (let row = 1; row <= numberOfPriceRulesAfterFilter; row++) {
          const textColumn = await boCatalogPriceRulesPage.getTextColumn(page, row, test.filterBy);
          expect(textColumn).to.contains(test.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}Reset`, baseContext);

        const numberOfPriceRulesAfterReset = await boCatalogPriceRulesPage.resetAndGetNumberOfLines(page);
        expect(numberOfPriceRulesAfterReset).to.equal(numberOfCatalogPriceRules + 21);
      });
    });

    [
      {
        testIdentifier: 'filterDateBeginning',
        filterBy: 'from',
        firstDate: today,
        secondDate: today,
      },
      {
        testIdentifier: 'filterDateEnd',
        filterBy: 'to',
        firstDate: today,
        secondDate: today,
      },
    ].forEach((test: {
      testIdentifier: string
      filterBy: string
      firstDate: string
      secondDate: string
    }) => {
      it('should filter by date Beginning and date End', async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        // Filter by date
        await boCatalogPriceRulesPage.filterByDate(page, test.filterBy, test.firstDate, test.secondDate);

        // Get number of elements
        const numberOfShoppingCartsAfterFilter = await boCatalogPriceRulesPage.getNumberOfElementInGrid(page);

        for (let row = 1; row <= numberOfShoppingCartsAfterFilter; row++) {
          const textColumn = await boCatalogPriceRulesPage.getTextColumn(
            page,
            row,
            test.filterBy,
          );
          expect(textColumn).to.contains(dateToCheck);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}Reset`, baseContext);

        const numberOfPriceRulesAfterReset = await boCatalogPriceRulesPage.resetAndGetNumberOfLines(page);
        expect(numberOfPriceRulesAfterReset).to.equal(numberOfCatalogPriceRules + 21);
      });
    });
  });

  // 3 - Sort Price rules table
  describe('Sort catalog price rules table', async () => {
    [
      {
        testIdentifier: 'sortByIdDesc', sortBy: 'id_specific_price_rule', sortDirection: 'down', isFloat: true,
      },
      {
        testIdentifier: 'sortByNameAsc', sortBy: 'a!name', sortDirection: 'up',
      },
      {
        testIdentifier: 'sortByNameDesc', sortBy: 'a!name', sortDirection: 'down',
      },
      {
        testIdentifier: 'sortByCurrencyAsc', sortBy: 'cul!name', sortDirection: 'up',
      },
      {
        testIdentifier: 'sortByCurrencyDesc', sortBy: 'cul!name', sortDirection: 'down',
      },
      {
        testIdentifier: 'sortByCountryAsc', sortBy: 'cl!name', sortDirection: 'up',
      },
      {
        testIdentifier: 'sortByCountryDesc', sortBy: 'cl!name', sortDirection: 'down',
      },
      {
        testIdentifier: 'sortByGroupAsc', sortBy: 'gl!name', sortDirection: 'up',
      },
      {
        testIdentifier: 'sortByGroupDesc', sortBy: 'gl!name', sortDirection: 'down',
      },
      {
        testIdentifier: 'sortByFromQuantityAsc', sortBy: 'from_quantity', sortDirection: 'up', isFloat: true,
      },
      {
        testIdentifier: 'sortByFromQuantityDesc', sortBy: 'from_quantity', sortDirection: 'down', isFloat: true,
      },
      {
        testIdentifier: 'sortByReducingTypeAsc', sortBy: 'a!reduction_type', sortDirection: 'up',
      },
      {
        testIdentifier: 'sortByReductionTypeDesc', sortBy: 'a!reduction_type', sortDirection: 'down',
      },
      {
        testIdentifier: 'sortByReductionAsc', sortBy: 'reduction', sortDirection: 'up', isFloat: true,
      },
      {
        testIdentifier: 'sortByReductionDesc', sortBy: 'reduction', sortDirection: 'down', isFloat: true,
      },
      {
        testIdentifier: 'sortByDateFromAsc', sortBy: 'from', sortDirection: 'up', isDate: true,
      },
      {
        testIdentifier: 'sortByDateFromDesc', sortBy: 'from', sortDirection: 'down', isDate: true,
      },
      {
        testIdentifier: 'sortByDateToAsc', sortBy: 'to', sortDirection: 'up', isDate: true,
      },
      {
        testIdentifier: 'sortByDateToDesc', sortBy: 'to', sortDirection: 'down', isDate: true,
      },
      {
        testIdentifier: 'sortByIdAsc', sortBy: 'id_specific_price_rule', sortDirection: 'up', isFloat: true,
      },
    ].forEach((test: {
      testIdentifier: string
      sortBy: string
      sortDirection: string
      isDate?: boolean
      isFloat?: boolean
    }) => {
      it(`should sort by '${test.sortBy}' '${test.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        const nonSortedTable = await boCatalogPriceRulesPage.getAllRowsColumnContent(page, test.sortBy);

        await boCatalogPriceRulesPage.sortTable(page, test.sortBy, test.sortDirection);

        const sortedTable = await boCatalogPriceRulesPage.getAllRowsColumnContent(page, test.sortBy);

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

  // 4 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await boCatalogPriceRulesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boCatalogPriceRulesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boCatalogPriceRulesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await boCatalogPriceRulesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 5 - Bulk select
  describe('Bulk Select', async () => {
    it('should select all', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkSelectAll', baseContext);

      await boCatalogPriceRulesPage.bulkSelectRows(page, true);

      const numRows = await boCatalogPriceRulesPage.getNumberOfElementInGrid(page);
      expect(numRows).to.gt(0);
      const numSelectedRows = await boCatalogPriceRulesPage.getSelectedRowsCount(page);
      expect(numSelectedRows).to.equals(numRows);
    });
    it('should unselect all', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkUnselectAll', baseContext);

      await boCatalogPriceRulesPage.bulkSelectRows(page, false);

      const numRows = await boCatalogPriceRulesPage.getNumberOfElementInGrid(page);
      expect(numRows).to.gt(0);
      const numSelectedRows = await boCatalogPriceRulesPage.getSelectedRowsCount(page);
      expect(numSelectedRows).to.equals(0);
    });
  });

  // 6 - Delete catalog price rules with bulk actions
  describe('Bulk delete catalog price rules', async () => {
    it('should bulk delete cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeletePriceRules', baseContext);

      const deleteTextResult = await boCatalogPriceRulesPage.bulkDeletePriceRules(page);
      expect(deleteTextResult).to.be.contains(boCatalogPriceRulesPage.successfulMultiDeleteMessage);
    });
  });
});
