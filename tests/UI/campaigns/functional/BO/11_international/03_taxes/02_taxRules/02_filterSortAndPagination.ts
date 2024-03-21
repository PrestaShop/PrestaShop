// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import taxesPage from '@pages/BO/international/taxes';
import taxRulesPage from '@pages/BO/international/taxes/taxRules';
import addTaxRulesPage from '@pages/BO/international/taxes/taxRules/add';

// Import data
import TaxRulesGroupData from '@data/faker/taxRulesGroup';

import {
  // Import data
  dataTaxRules,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_taxes_taxRules_filterSortAndPagination';

/*
Filter tax rules table by id, name and enabled
Sort table by id and name
Create 16 new tax rules
Test pagination next and previous
Delete the created tax rules by bulk actions
 */
describe('BO - International - Tax rules : Filter, sort and pagination', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTaxRules: number = 0;

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

  it('should go to \'International > Taxes\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.taxesLink,
    );

    const pageTitle = await taxesPage.getPageTitle(page);
    expect(pageTitle).to.contains(taxesPage.pageTitle);
  });

  it('should go to \'Tax Rules\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTaxRulesPage', baseContext);

    await taxesPage.goToTaxRulesPage(page);

    const pageTitle = await taxRulesPage.getPageTitle(page);
    expect(pageTitle).to.contains(taxRulesPage.pageTitle);
  });

  it('should reset all filters and get number of Tax rules in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTaxRules = await taxRulesPage.resetAndGetNumberOfLines(page);
    expect(numberOfTaxRules).to.be.above(0);
  });

  // 1 - Filter tax rules
  describe('Filter tax rules table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_tax_rules_group',
            filterValue: dataTaxRules[3].id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: dataTaxRules[1].name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByStatus',
            filterType: 'select',
            filterBy: 'active',
            filterValue: '1',
          },
        expected: 'Enabled',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await taxRulesPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfLinesAfterFilter = await taxRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfLinesAfterFilter).to.be.at.most(numberOfTaxRules);

        for (let row = 1; row <= numberOfLinesAfterFilter; row++) {
          const textColumn = await taxRulesPage.getTextColumnFromTable(page, row, test.args.filterBy);

          if (test.expected !== undefined) {
            expect(textColumn).to.contains(test.expected);
          } else {
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfLinesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
        expect(numberOfLinesAfterReset).to.equal(numberOfTaxRules);
      });
    });
  });

  // 2 - Sort tax rules table
  describe('Sort tax rules table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_tax_rules_group', sortDirection: 'down', isFloat: true,
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
          testIdentifier: 'sortByIdAsc', sortBy: 'id_tax_rules_group', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await taxRulesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await taxRulesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await taxRulesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'up') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'up') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 3 - Create 16 tax rules
  const creationTests: number[] = new Array(16).fill(0, 0, 16);

  creationTests.forEach((test: number, index: number) => {
    describe(`Create tax rule n°${index + 1} in BO`, async () => {
      const taxRuleData = new TaxRulesGroupData({name: `todelete${index}`});

      it('should go to add new tax rule group page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddTaxRuleGroupPage${index}`, baseContext);

        await taxRulesPage.goToAddNewTaxRulesGroupPage(page);

        const pageTitle = await addTaxRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(addTaxRulesPage.pageTitleCreate);
      });

      it('should create tax rule group and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createTaxRule${index}`, baseContext);

        const textResult = await addTaxRulesPage.createEditTaxRulesGroup(page, taxRuleData);
        expect(textResult).to.contains(addTaxRulesPage.successfulCreationMessage);

        await taxesPage.goToTaxRulesPage(page);

        const numberOfLinesAfterCreation = await taxRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfLinesAfterCreation).to.be.equal(numberOfTaxRules + 1 + index);
      });
    });
  });

  // 4 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await taxRulesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await taxRulesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await taxRulesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await taxRulesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 5 : Delete tax rules created with bulk actions
  describe('Delete tax rules with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await taxRulesPage.filterTable(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfLinesAfterFilter = await taxRulesPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfLinesAfterFilter; i++) {
        const textColumn = await taxRulesPage.getTextColumnFromTable(
          page,
          i,
          'name',
        );
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete tax rules with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCarriers', baseContext);

      const deleteTextResult = await taxRulesPage.bulkDeleteTaxRules(page);
      expect(deleteTextResult).to.be.contains(taxRulesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfLinesAfterReset = await taxRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLinesAfterReset).to.be.equal(numberOfTaxRules);
    });
  });
});
