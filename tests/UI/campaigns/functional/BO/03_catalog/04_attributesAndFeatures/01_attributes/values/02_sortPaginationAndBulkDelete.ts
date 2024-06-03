// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import attributesPage from '@pages/BO/catalog/attributes';
import addValuePage from '@pages/BO/catalog/attributes/addValue';
import viewAttributePage from '@pages/BO/catalog/attributes/view';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerAttributeValue,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_attributes_values_sortPaginationAndBulkDelete';

let browserContext: BrowserContext;
let page: Page;
let numberOfValues: number = 0;
let idAttribute: number = 0;

/*
Go to Attributes & Features page
Go to view attribute 'Color'
Create 7 new values
Pagination next and previous
Sort values table by ID, Value, Color and Position
Delete the created values by bulk actions
 */
describe('BO - Catalog - Attributes & Features : Sort, pagination and bulk delete attribute values', async () => {
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

  it('should go to \'Catalog > Attributes & Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.attributesAndFeaturesLink,
    );
    await attributesPage.closeSfToolBar(page);

    const pageTitle = await attributesPage.getPageTitle(page);
    expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAttributeFilter', baseContext);

    const numberOfAttributesAfterReset = await attributesPage.resetAndGetNumberOfLines(page);
    expect(numberOfAttributesAfterReset).to.be.above(0);
  });

  it('should filter list of attributes by name \'Color\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDeleteAttributes', baseContext);

    await attributesPage.filterTable(page, 'name', 'Color');

    const textColumn = await attributesPage.getTextColumn(page, 1, 'name');
    expect(textColumn).to.contains('Color');

    idAttribute = parseInt(await attributesPage.getTextColumn(page, 1, 'id_attribute_group'), 10);
    expect(idAttribute).to.be.gt(0);
  });

  it('should view attribute \'Color\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewAttributeColor1', baseContext);

    await attributesPage.viewAttribute(page, 1);

    const pageTitle = await viewAttributePage.getPageTitle(page);
    expect(pageTitle).to.equal(viewAttributePage.pageTitle('Color'));

    numberOfValues = await viewAttributePage.resetAndGetNumberOfLines(page);
    expect(numberOfValues).to.be.above(0);
  });

  // 1 : Create 7 new values
  const creationTests: number[] = new Array(7).fill(0, 0, 7);
  describe('Create 7 new values in BO', async () => {
    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewValuePage', baseContext);

      await viewAttributePage.goToAddNewValuePage(page);

      const pageTitle = await addValuePage.getPageTitle(page);
      expect(pageTitle).to.contains(addValuePage.createPageTitle);
    });

    creationTests.forEach((test: number, index: number) => {
      it(`should create value n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createNewValue${index}`, baseContext);

        const createValueData: FakerAttributeValue = new FakerAttributeValue({
          attributeID: idAttribute,
          attributeName: 'Color',
          value: `todelete${index}`,
        });

        const textResult = await addValuePage.addEditValue(page, createValueData, index !== 6);
        expect(textResult).to.contains(attributesPage.successfulCreationMessage);
      });
    });

    it('should check number of values after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberAfterCreation', baseContext);

      const pageTitle = await viewAttributePage.getPageTitle(page);
      expect(pageTitle).to.equal(viewAttributePage.pageTitle('Color'));

      const numberOfValuesAfterCreation = await viewAttributePage.resetAndGetNumberOfLines(page);
      expect(numberOfValuesAfterCreation).to.equal(numberOfValues + 7);
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await viewAttributePage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await viewAttributePage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await viewAttributePage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await viewAttributePage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort values
  describe('Sort values table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_attribute', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByColorAsc', sortBy: 'color', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByColorDesc', sortBy: 'color', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionAsc', sortBy: 'position', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionDesc', sortBy: 'position', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_attribute', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await viewAttributePage.getAllRowsColumnContent(page, test.args.sortBy);

        await viewAttributePage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await viewAttributePage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 4 : Delete created values by bulk actions
  describe('Bulk delete values', async () => {
    it('should filter by value name \'toDelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await viewAttributePage.filterTable(page, 'name', 'toDelete');

      const numberOfValuesAfterFilter = await viewAttributePage.getNumberOfElementInGrid(page);
      expect(numberOfValuesAfterFilter).to.be.at.most(numberOfValues);
    });

    it('should delete values with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAttributes', baseContext);

      const deleteTextResult = await viewAttributePage.bulkDeleteValues(page);
      expect(deleteTextResult).to.be.contains(viewAttributePage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfValuesAfterReset = await viewAttributePage.resetAndGetNumberOfLines(page);
      expect(numberOfValuesAfterReset).to.equal(numberOfValues);
    });
  });
});
