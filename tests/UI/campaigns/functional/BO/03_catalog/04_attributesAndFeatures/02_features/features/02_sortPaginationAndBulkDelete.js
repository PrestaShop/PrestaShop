require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const featuresPage = require('@pages/BO/catalog/features');
const addFeaturePage = require('@pages/BO/catalog/features/addFeature');

// Import data
const {FeatureData} = require('@data/faker/featureAndValue');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_features_features_sortPaginationAndBulkDelete';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfFeatures = 0;

/*
Go to Attributes & Features page
Go to Features tab
Create 18 new features
Pagination next and previous
Sort features table by ID, Name and Position
Delete the created features by bulk actions
 */
describe('BO - Catalog - Attributes & Features : Sort, pagination and delete by bulk actions features', async () => {
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

    numberOfFeatures = await featuresPage.resetAndGetNumberOfLines(page);
    await expect(numberOfFeatures).to.be.above(0);
  });

  // 1 : Create 19 new features
  const creationTests = new Array(19).fill(0, 0, 19);
  describe('Create 19 new features in BO', async () => {
    creationTests.forEach((test, index) => {
      const createFeatureData = new FeatureData({name: `todelete${index}`});
      it('should go to add new feature page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewFeaturePage${index}`, baseContext);

        await featuresPage.goToAddFeaturePage(page);

        const pageTitle = await addFeaturePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addFeaturePage.createPageTitle);
      });

      it(`should create feature n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createNewFeature${index}`, baseContext);

        const textResult = await addFeaturePage.setFeature(page, createFeatureData);
        await expect(textResult).to.contains(featuresPage.successfulCreationMessage);
      });
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await featuresPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await featuresPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await featuresPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await featuresPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 : Sort values
  describe('Sort values table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_feature', sortDirection: 'down', isFloat: true,
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
          testIdentifier: 'sortByIdAsc', sortBy: 'id_feature', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await featuresPage.getAllRowsColumnContent(page, test.args.sortBy);

        await featuresPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await featuresPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await featuresPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete created features by bulk actions
  describe('Bulk delete features', async () => {
    it('should filter by feature name \'toDelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await featuresPage.filterTable(page, 'b!name', 'toDelete');

      const numberOfFeaturesAfterFilter = await featuresPage.getNumberOfElementInGrid(page);
      await expect(numberOfFeaturesAfterFilter).to.be.equal(19);
    });

    it('should delete features by Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteFeatures', baseContext);

      const deleteTextResult = await featuresPage.bulkDeleteFeatures(page);
      await expect(deleteTextResult).to.be.contains(featuresPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfFeaturesAfterReset = await featuresPage.resetAndGetNumberOfLines(page);
      await expect(numberOfFeaturesAfterReset).to.equal(numberOfFeatures);
    });
  });
});
