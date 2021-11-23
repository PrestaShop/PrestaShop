require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const featuresPage = require('@pages/BO/catalog/features');
const viewFeaturePage = require('@pages/BO/catalog/features/view');
const addValuePage = require('@pages/BO/catalog/features/addValue');

// Import data
const {ValueData} = require('@data/faker/featureAndValue');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_features_values_sortPaginationAndBulkDelete';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfValues = 0;

/*
Go to Attributes & Features page
Go to Features tab
View the feature 'Composition'
Create 15 new values
Pagination next and previous
Sort features table by ID and Name
Delete the created value by bulk actions
 */
describe('BO - Catalog - Catalog > Attributes & Features : Sort, pagination and delete by bulk actions '
  + 'feature values', async () => {
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

  it('should go to Features page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await attributesPage.goToFeaturesPage(page);

    const pageTitle = await featuresPage.getPageTitle(page);
    await expect(pageTitle).to.contains(featuresPage.pageTitle);
  });

  it('should filter list of features by name \'Composition\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDeleteAttributes', baseContext);

    await featuresPage.filterTable(page, 'b!name', 'Composition');

    const textColumn = await featuresPage.getTextColumn(page, 1, 'b!name');
    await expect(textColumn).to.contains('Composition');
  });

  it('should view feature \'Composition\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewFeatureComposition1', baseContext);

    await featuresPage.viewFeature(page, 1);

    const pageTitle = await viewFeaturePage.getPageTitle(page);
    await expect(pageTitle).to.contains(`${viewFeaturePage.pageTitle} Composition`);

    numberOfValues = await viewFeaturePage.resetAndGetNumberOfLines(page);
    await expect(numberOfValues).to.be.above(0);
  });

  // 1 : Create 15 new values
  const creationTests = new Array(15).fill(0, 0, 15);
  describe('Create 15 new values in BO', async () => {
    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewValuePage', baseContext);

      await viewFeaturePage.goToAddNewValuePage(page);

      const pageTitle = await addValuePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addValuePage.createPageTitle);
    });

    creationTests.forEach((test, index) => {
      const createValueData = new ValueData({featureName: 'Composition', value: `todelete${index}`});
      it(`should create value n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createNewValue${index}`, baseContext);

        let textResult;
        if (index === 14) {
          textResult = await addValuePage.addEditValue(page, createValueData, false);
          await expect(textResult).to.contains(viewFeaturePage.successfulCreationMessage);
        } else {
          await addValuePage.addEditValue(page, createValueData, true);
        }
      });
    });

    it('should view feature \'Composition\' and check number of values after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewFeatureComposition2', baseContext);

      await featuresPage.viewFeature(page, 1);

      const pageTitle = await viewFeaturePage.getPageTitle(page);
      await expect(pageTitle).to.contains(`${viewFeaturePage.pageTitle} Composition`);

      const numberOfValuesAfterCreation = await viewFeaturePage.resetAndGetNumberOfLines(page);
      await expect(numberOfValuesAfterCreation).to.equal(numberOfValues + 15);
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await viewFeaturePage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await viewFeaturePage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await viewFeaturePage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await viewFeaturePage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 : Sort values
  describe('Sort values table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_feature_value', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'value', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'value', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_feature_value', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await viewFeaturePage.getAllRowsColumnContent(page, test.args.sortBy);

        await viewFeaturePage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await viewFeaturePage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await viewFeaturePage.sortArray(nonSortedTable, test.args.isFloat);

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

      await viewFeaturePage.filterTable(page, 'value', 'toDelete');

      const numberOfValuesAfterFilter = await viewFeaturePage.getNumberOfElementInGrid(page);
      await expect(numberOfValuesAfterFilter).to.be.equal(15);
    });

    it('should delete values with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteFeatures', baseContext);

      const deleteTextResult = await viewFeaturePage.bulkDeleteValues(page);
      await expect(deleteTextResult).to.be.contains(viewFeaturePage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfValuesAfterReset = await viewFeaturePage.resetAndGetNumberOfLines(page);
      await expect(numberOfValuesAfterReset).to.equal(numberOfValues);
    });
  });
});
