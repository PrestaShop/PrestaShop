// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import attributesPage from '@pages/BO/catalog/attributes';
import featuresPage from '@pages/BO/catalog/features';
import addFeaturePage from '@pages/BO/catalog/features/addFeature';
import viewFeaturePage from '@pages/BO/catalog/features/view';
import addValuePage from '@pages/BO/catalog/features/addValue';

// Import data
import FeatureData from '@data/faker/feature';
import FeatureValueData from '@data/faker/featureValue';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_CRUDFeaturesAndValues';

/*
Scenario:
- Create feature
- View feature
- Create 2 values
- Edit value
- Edit feature
- Delete value
- Delete feature
 */
describe('BO - Catalog - Attributes & Features : CRUD features and values', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfFeatures: number = 0;
  const numberOfValues: number = 0;
  const createFeatureData: FeatureData = new FeatureData({name: 'Texture'});
  const editFeatureData: FeatureData = new FeatureData({name: 'TextureEdit', metaTitle: 'Feature texture'});
  const createFeatureValueData: FeatureValueData = new FeatureValueData({
    featureName: createFeatureData.name,
    value: 'Smooth',
  });
  const createSecondFeatureValueData: FeatureValueData = new FeatureValueData({
    featureName: createFeatureData.name,
    value: 'Rough',
  });
  const editSecondFeatureValueData: FeatureValueData = new FeatureValueData({
    featureName: createFeatureData.name,
    value: 'Feature value smooth',
  });

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

  it('should go to \'Catalog > Attributes & features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.attributesAndFeaturesLink,
    );
    await attributesPage.closeSfToolBar(page);

    const pageTitle = await attributesPage.getPageTitle(page);
    expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  it('should go to Features page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await attributesPage.goToFeaturesPage(page);

    const pageTitle = await featuresPage.getPageTitle(page);
    expect(pageTitle).to.contains(featuresPage.pageTitle);
  });

  it('should reset all filters and get number of features in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfFeatures = await featuresPage.resetAndGetNumberOfLines(page);
    expect(numberOfFeatures).to.be.above(0);
  });

  describe('Create feature', async () => {
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

  describe('View feature', async () => {
    it('should filter list of features by the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterFeature', baseContext);

      await featuresPage.filterTable(page, 'name', createFeatureData.name);

      const textColumn = await featuresPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createFeatureData.name);
    });

    it('should view feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewFeature', baseContext);

      await featuresPage.viewFeature(page, 1);

      const pageTitle = await viewFeaturePage.getPageTitle(page);
      expect(pageTitle).to.contains(`${createFeatureData.name} • ${global.INSTALL.SHOP_NAME}`);
    });
  });

  describe('Create value', async () => {
    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewValuePage', baseContext);

      await viewFeaturePage.goToAddNewValuePage(page);

      const pageTitle = await addValuePage.getPageTitle(page);
      expect(pageTitle).to.eq(addValuePage.createPageTitle);
    });

    it('should create value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewValue', baseContext);

      const textResult = await addValuePage.addEditValue(page, createFeatureValueData, true);
      expect(textResult).to.contains(addValuePage.successfulCreationMessage);
    });

    it('should create a second value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSecondValue', baseContext);

      const textResult = await addValuePage.addEditValue(page, createSecondFeatureValueData, false);
      expect(textResult).to.contains(viewFeaturePage.successfulCreationMessage);
    });
  });

  describe('View value', async () => {
    it('should view feature and check number of values after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewFeature1', baseContext);

      const pageTitle = await viewFeaturePage.getPageTitle(page);
      expect(pageTitle).to.contains(`${createFeatureData.name} • ${global.INSTALL.SHOP_NAME}`);

      const numberOfValuesAfterCreation = await viewFeaturePage.resetAndGetNumberOfLines(page);
      expect(numberOfValuesAfterCreation).to.equal(numberOfValues + 2);
    });
  });

  describe('Update value', async () => {
    it('should go to edit the second value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditValuePage', baseContext);

      await viewFeaturePage.goToEditValuePage(page, 2);

      const pageTitle = await addValuePage.getPageTitle(page);
      expect(pageTitle).to.eq(addValuePage.editPageTitle);
    });

    it('should update the second value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editValue', baseContext);

      const textResult = await addValuePage.addEditValue(page, editSecondFeatureValueData, false);
      expect(textResult).to.contains(viewFeaturePage.successfulUpdateMessage);
    });
  });

  describe('Update feature', async () => {
    it('should click on \'Back to the list\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToTheListForUpdate', baseContext);

      await viewFeaturePage.clickOnBackToTheListButton(page);

      const pageTitle = await featuresPage.getPageTitle(page);
      expect(pageTitle).to.contains(featuresPage.pageTitle);
    });

    it('should filter list of features by the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterFeatureForUpdate', baseContext);

      await featuresPage.filterTable(page, 'name', createFeatureData.name);

      const textColumn = await featuresPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createFeatureData.name);
    });

    it('should edit the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editFeature', baseContext);

      await featuresPage.clickOnEditFeature(page, 1);

      const textResult = await addFeaturePage.setFeature(page, editFeatureData);
      expect(textResult).to.contains(addFeaturePage.successfulUpdateMessage);
    });
  });

  describe('Delete value', async () => {
    it('should view feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewFeature2', baseContext);

      await featuresPage.viewFeature(page, 1);

      const pageTitle = await viewFeaturePage.getPageTitle(page);
      expect(pageTitle).to.contains(`${editFeatureData.name} • ${global.INSTALL.SHOP_NAME}`);
    });

    it('should delete the second value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteValue', baseContext);

      const textResult = await viewFeaturePage.deleteValue(page, 2);
      expect(textResult).to.contains(viewFeaturePage.successfulDeleteMessage);
    });
  });

  describe('Delete feature', async () => {
    it('should click on \'Back to the list\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToTheListForDelete', baseContext);

      await viewFeaturePage.clickOnBackToTheListButton(page);

      const pageTitle = await featuresPage.getPageTitle(page);
      expect(pageTitle).to.contains(featuresPage.pageTitle);
    });

    it('should filter list of features by the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterFeatureForDelete', baseContext);

      await featuresPage.filterTable(page, 'name', editFeatureData.name);

      const textColumn = await featuresPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(editFeatureData.name);
    });

    it('should delete the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFeature', baseContext);

      const textResult = await featuresPage.deleteFeature(page, 1);
      expect(textResult).to.contains(featuresPage.successfulDeleteMessage);
    });
  });
});
