import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {createFeatureTest, bulkDeleteFeaturesTest} from '@commonTests/BO/catalog/features';

import {
  boAttributesPage,
  boDashboardPage,
  boFeaturesPage,
  boLoginPage,
  type BrowserContext,
  FakerFeature,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  let sortColumnName: string = 'id_feature';

  // PRE-condition : Create 19 features
  const creationTests: number[] = new Array(19).fill(0, 0, 19);
  creationTests.forEach((test: number, index: number) => {
    const createFeatureData: FakerFeature = new FakerFeature({name: `toDelete${index}`});
    createFeatureTest(createFeatureData, `${baseContext}_preTest${index}`);
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.attributesAndFeaturesLink,
      );
      await boAttributesPage.closeSfToolBar(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should go to Features page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

      await boAttributesPage.goToFeaturesPage(page);

      const pageTitle = await boFeaturesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boFeaturesPage.pageTitle);

      numberOfFeatures = await boFeaturesPage.resetAndGetNumberOfLines(page);
      expect(numberOfFeatures).to.be.above(0);
    });

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await boFeaturesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal(1);
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boFeaturesPage.paginationNext(page);
      expect(paginationNumber).to.equal(2);
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boFeaturesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal(1);
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await boFeaturesPage.selectPaginationLimit(page, 50);
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

        const nonSortedTable = await boFeaturesPage.getAllRowsColumnContent(page, test.args.sortBy, sortColumnName);

        await boFeaturesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boFeaturesPage.getAllRowsColumnContent(page, test.args.sortBy, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }

        // Previous Sort Column
        sortColumnName = test.args.sortBy;
      });
    });
  });

  // POST-condition : Delete created features
  bulkDeleteFeaturesTest('toDelete', `${baseContext}_postTest_1`);
});
