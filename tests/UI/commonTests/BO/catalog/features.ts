import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boDashboardPage,
  boFeaturesPage,
  boFeaturesCreatePage,
  boLoginPage,
  type BrowserContext,
  type FakerFeature,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;
let numberOfFeatures: number;
let numberOfFeaturesToDelete: number;

/**
 * Function to create feature
 * @param createFeatureData {FakerFeature} Data to set to create feature
 * @param baseContext {string} String to identify the test
 */
function createFeatureTest(createFeatureData: FakerFeature, baseContext: string = 'commonTests-createFeatureTest'): void {
  describe(`PRE-TEST: Create feature '${createFeatureData.name}'`, async () => {
    // before and after functions
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

    it('should go to add new feature page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewFeaturePage', baseContext);

      await boFeaturesPage.goToAddFeaturePage(page);

      const pageTitle = await boFeaturesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boFeaturesCreatePage.createPageTitle);
    });

    it('should create feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewFeature', baseContext);

      const textResult = await boFeaturesCreatePage.setFeature(page, createFeatureData);
      expect(textResult).to.contains(boFeaturesPage.successfulCreationMessage);
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

    it(`should filter by feature name '${featureName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await boFeaturesPage.filterTable(page, 'name', featureName);

      numberOfFeaturesToDelete = await boFeaturesPage.getNumberOfElementInGrid(page);
      expect(numberOfFeaturesToDelete).to.be.above(0);
    });

    it('should delete features by Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteFeatures', baseContext);

      const deleteTextResult = await boFeaturesPage.bulkDeleteFeatures(page);
      expect(deleteTextResult).to.be.contains(boFeaturesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfFeaturesAfterDelete = await boFeaturesPage.resetAndGetNumberOfLines(page);
      expect(numberOfFeaturesAfterDelete).to.equal(numberOfFeatures - numberOfFeaturesToDelete);
    });
  });
}

export {createFeatureTest, bulkDeleteFeaturesTest};
