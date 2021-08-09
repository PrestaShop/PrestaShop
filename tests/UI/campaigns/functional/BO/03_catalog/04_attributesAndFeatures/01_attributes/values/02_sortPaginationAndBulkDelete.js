require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const viewAttributePage = require('@pages/BO/catalog/attributes/view');
const addValuePage = require('@pages/BO/catalog/attributes/addValue');

// Import data
const {ValueData} = require('@data/faker/attributeAndValue');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_attributes_values_sortPaginationAndBulkDelete';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfValues = 0;

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

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.attributesAndFeaturesLink,
    );

    await attributesPage.closeSfToolBar(page);

    const pageTitle = await attributesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  it('should filter list of attributes by name \'Color\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDeleteAttributes', baseContext);

    await attributesPage.filterTable(page, 'b!name', 'Color');

    const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
    await expect(textColumn).to.contains('Color');
  });

  it('should view attribute \'Color\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewAttributeColor1', baseContext);

    await attributesPage.viewAttribute(page, 1);

    const pageTitle = await viewAttributePage.getPageTitle(page);
    await expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} Color`);

    numberOfValues = await viewAttributePage.resetAndGetNumberOfLines(page);
    await expect(numberOfValues).to.be.above(0);
  });

  // 1 : Create 7 new values
  const creationTests = new Array(7).fill(0, 0, 7);
  describe('Create 7 new values in BO', async () => {
    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewValuePage', baseContext);

      await viewAttributePage.goToAddNewValuePage(page);

      const pageTitle = await addValuePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addValuePage.createPageTitle);
    });

    creationTests.forEach((test, index) => {
      const createValueData = new ValueData({attributeName: 'Color', value: `todelete${index}`});
      it(`should create value n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createNewValue${index}`, baseContext);

        let textResult;
        if (index !== 6) {
          textResult = await addValuePage.addEditValue(page, createValueData, true);
        } else {
          textResult = await addValuePage.addEditValue(page, createValueData, false);
        }
        await expect(textResult).to.contains(attributesPage.successfulCreationMessage);
      });
    });

    it('should view attribute \'Color\' and check number of values after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewAttributeColor2', baseContext);

      await attributesPage.viewAttribute(page, 1);

      const pageTitle = await viewAttributePage.getPageTitle(page);
      await expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} Color`);

      const numberOfValuesAfterCreation = await viewAttributePage.resetAndGetNumberOfLines(page);
      await expect(numberOfValuesAfterCreation).to.equal(numberOfValues + 7);
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await viewAttributePage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await viewAttributePage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await viewAttributePage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await viewAttributePage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 : Sort values
  describe('Sort values table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_attribute', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'b!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'b!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByColorAsc', sortBy: 'a!color', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByColorDesc', sortBy: 'a!color', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionAsc', sortBy: 'a!position', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionDesc', sortBy: 'a!position', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_attribute', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await viewAttributePage.getAllRowsColumnContent(page, test.args.sortBy);

        await viewAttributePage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await viewAttributePage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await viewAttributePage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete created values by bulk actions
  describe('Bulk delete values', async () => {
    it('should filter by value name \'toDelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await viewAttributePage.filterTable(page, 'b!name', 'toDelete');

      const numberOfValuesAfterFilter = await viewAttributePage.getNumberOfElementInGrid(page);
      await expect(numberOfValuesAfterFilter).to.be.at.most(numberOfValues);
    });

    it('should delete values with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAttributes', baseContext);

      const deleteTextResult = await viewAttributePage.bulkDeleteValues(page);
      await expect(deleteTextResult).to.be.contains(viewAttributePage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfValuesAfterReset = await viewAttributePage.resetAndGetNumberOfLines(page);
      await expect(numberOfValuesAfterReset).to.equal(numberOfValues);
    });
  });
});
