// Import utils
import testContext from '@utils/testContext';

// Import pages
import featuresPage from '@pages/BO/catalog/features';
import addFeaturePage from '@pages/BO/catalog/features/addFeature';

import {expect} from 'chai';
import {
  boAttributesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerFeature,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_features_changePosition';

/*
Go To features page
View first feature
Change first feature position to 3
Reset value position
 */
describe('BO - Catalog - Attributes & Features : Change feature position', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const createFeatureData: FakerFeature = new FakerFeature({name: 'Texture'});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('PRE-TEST: Create new feature', async () => {
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

      const pageTitle = await featuresPage.getPageTitle(page);
      expect(pageTitle).to.contains(featuresPage.pageTitle);
    });

    it('should go to add new feature page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewFeaturePage', baseContext);

      await featuresPage.goToAddFeaturePage(page);

      const pageTitle = await addFeaturePage.getPageTitle(page);
      expect(pageTitle).to.eq(addFeaturePage.createPageTitle);
    });

    it('should create feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewFeature', baseContext);

      const textResult = await addFeaturePage.setFeature(page, createFeatureData);
      expect(textResult).to.contains(featuresPage.successfulCreationMessage);
    });
  });

  describe('Change position', async () => {
    it('should sort by \'position\' \'asc\' And check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sortByPosition', baseContext);

      const nonSortedTable = await featuresPage.getAllRowsColumnContent(page, 'position', 'id_feature');

      await featuresPage.sortTable(page, 'position', 'asc');

      const sortedTable = await featuresPage.getAllRowsColumnContent(page, 'position', 'position');

      const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
      const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

      const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

      expect(sortedTableFloat).to.deep.equal(expectedResult);
    });

    it('should change first feature position to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeFeaturePosition', baseContext);

      // Get first row feature name
      const firstRowFeatureName = await featuresPage.getTextColumn(page, 1, 'name', 'position');

      // Change position and check successful message
      const textResult = await featuresPage.changePosition(page, 1, 2);
      expect(textResult, 'Unable to change position').to.contains(featuresPage.successfulUpdateMessage);

      // Get second row feature name and check if is equal the first row feature name before changing position
      const secondRowFeatureName = await featuresPage.getTextColumn(page, 2, 'name', 'position');
      expect(secondRowFeatureName, 'Changing position was done wrongly').to.equal(firstRowFeatureName);
    });

    it('should change second feature position to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFeaturePosition', baseContext);

      // Get third row feature name
      const secondRowFeatureName = await featuresPage.getTextColumn(page, 2, 'name', 'position');

      // Change position and check successful message
      const textResult = await featuresPage.changePosition(page, 2, 3);
      expect(textResult, 'Unable to change position').to.contains(featuresPage.successfulUpdateMessage);

      // Get first row feature name and check if is equal the first row feature name before changing position
      const thirdRowFeatureName = await featuresPage.getTextColumn(page, 3, 'name', 'position');
      expect(thirdRowFeatureName, 'Changing position was done wrongly').to.equal(secondRowFeatureName);
    });

    it('should reset the sort to \'id_feature\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sortByIdFeature', baseContext);

      const nonSortedTable = await featuresPage.getAllRowsColumnContent(page, 'id_feature', 'position');

      await featuresPage.sortTable(page, 'id_feature', 'asc');

      const sortedTable = await featuresPage.getAllRowsColumnContent(page, 'id_feature', 'id_feature');

      const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
      const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

      const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

      expect(sortedTableFloat).to.deep.equal(expectedResult);
    });
  });

  describe('POST-TEST: Delete created feature', async () => {
    it('should filter list of features by the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterFeature', baseContext);

      await featuresPage.filterTable(page, 'name', createFeatureData.name);

      const textColumn = await featuresPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createFeatureData.name);
    });

    it('should delete the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFeature', baseContext);

      const textResult = await featuresPage.deleteFeature(page, 1);
      expect(textResult).to.contains(featuresPage.successfulDeleteMessage);
    });

    it('should reset all filters and get number of features in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfFeatures = await featuresPage.resetAndGetNumberOfLines(page);
      expect(numberOfFeatures).to.be.above(0);
    });
  });
});
