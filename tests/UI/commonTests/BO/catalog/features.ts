// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import featuresPage from '@pages/BO/catalog/features';
import addFeaturePage from '@pages/BO/catalog/features/addFeature';
import attributesPage from '@pages/BO/catalog/attributes';

// Import data
import FeatureData from '@data/faker/feature';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;
let numberOfFeatures: number;
let numberOfFeaturesToDelete: number;

/**
 * Function to create feature
 * @param createFeatureData {FeatureData} Data to set to create feature
 * @param baseContext {string} String to identify the test
 */
function createFeatureTest(createFeatureData: FeatureData, baseContext: string = 'commonTests-createFeatureTest'): void {
  describe(`PRE-TEST: Create feature '${createFeatureData.name}'`, async () => {
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

    it('should go to add new feature page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewFeaturePage', baseContext);

      await featuresPage.goToAddFeaturePage(page);

      const pageTitle = await addFeaturePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addFeaturePage.createPageTitle);
    });

    it('should create feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewFeature', baseContext);

      const textResult = await addFeaturePage.setFeature(page, createFeatureData);
      await expect(textResult).to.contains(featuresPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to bulk delete product
 * @param featureName {string} Value to set on feature name input
 * @param baseContext {string} String to identify the test
 */
function bulkDeleteFeaturesTest(featureName: string, baseContext: string = 'commonTests-bulkDeleteFeaturesTest'): void {
  describe('POST-TEST: Bulk delete features', async () => {
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

    it(`should filter by feature name '${featureName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await featuresPage.filterTable(page, 'name', featureName);

      const numberOfFeaturesAfterFilter = await featuresPage.getNumberOfElementInGrid(page);
      await expect(numberOfFeaturesAfterFilter).to.be.equal(19);
    });

    it('should get the number of features to delete', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberToDelete', baseContext);

      numberOfFeaturesToDelete = await featuresPage.getNumberOfElementInGrid(page);
      await expect(numberOfFeaturesToDelete).to.be.above(0);
    });

    it('should delete features by Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteFeatures', baseContext);

      const deleteTextResult = await featuresPage.bulkDeleteFeatures(page);
      await expect(deleteTextResult).to.be.contains(featuresPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfFeaturesAfterDelete = await featuresPage.resetAndGetNumberOfLines(page);
      await expect(numberOfFeaturesAfterDelete).to.equal(numberOfFeatures - numberOfFeaturesToDelete);
    });
  });
}

export {createFeatureTest, bulkDeleteFeaturesTest};
