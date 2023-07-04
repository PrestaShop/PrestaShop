// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createFeatureTest, bulkDeleteFeaturesTest} from '@commonTests/BO/catalog/features';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import attributesPage from '@pages/BO/catalog/attributes';
import featuresPage from '@pages/BO/catalog/features';

// Import data
import FeatureData from '@data/faker/feature';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_features_sortPaginationAndBulkDelete';

/*
Pre-condition:
- Create 18 new features
Scenario:
- Pagination next and previous
- Sort features table by ID, Name and Position
Post-condition:
- Delete the created features by bulk actions
 */
describe('BO - Catalog - Attributes & Features : Sort, pagination and bulk delete features', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfFeatures: number = 0;

  // PRE-condition : Create 19 features
  const creationTests: number[] = new Array(19).fill(0, 0, 19);
  creationTests.forEach((test: number, index: number) => {
    if (index > 18) {
      const createFeatureData: FeatureData = new FeatureData({name: `toDelete${index}`});
      createFeatureTest(createFeatureData, `${baseContext}_preTest${index}`);
    }
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
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

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await featuresPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal(1);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await featuresPage.paginationNext(page);
      expect(paginationNumber).to.equal(2);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await featuresPage.paginationPrevious(page);
      expect(paginationNumber).to.equal(1);
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await featuresPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal(1);
    });
  });

  // 3 : Sort values
  describe('Sort values table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_feature', sortDirection: 'desc', isFloat: true,
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
          testIdentifier: 'sortByIdAsc', sortBy: 'id_feature', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await featuresPage.getAllRowsColumnContent(page, test.args.sortBy);

        await featuresPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await featuresPage.getAllRowsColumnContent(page, test.args.sortBy);

        console.log(nonSortedTable);
        console.log(sortedTable);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // POST-condition : Delete created features
  bulkDeleteFeaturesTest('toDelete', `${baseContext}_postTest_1`);
});
