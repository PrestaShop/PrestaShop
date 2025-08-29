import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boDashboardPage,
  boFeaturesPage,
  boFeaturesCreatePage,
  boFeaturesValueCreatePage,
  boFeaturesViewPage,
  boLoginPage,
  type BrowserContext,
  FakerFeature,
  FakerFeatureValue,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const createFeatureData: FakerFeature = new FakerFeature({name: 'Texture'});
  const editFeatureData: FakerFeature = new FakerFeature({name: 'TextureEdit', metaTitle: 'Feature texture'});
  const createFeatureValueData: FakerFeatureValue = new FakerFeatureValue({
    featureName: createFeatureData.name,
    value: 'Smooth',
  });
  const createSecondFeatureValueData: FakerFeatureValue = new FakerFeatureValue({
    featureName: createFeatureData.name,
    value: 'Rough',
  });
  const editSecondFeatureValueData: FakerFeatureValue = new FakerFeatureValue({
    featureName: createFeatureData.name,
    value: 'Feature value smooth',
  });

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

  it('should go to \'Catalog > Attributes & features\' page', async function () {
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
  });

  it('should reset all filters and get number of features in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfFeatures = await boFeaturesPage.resetAndGetNumberOfLines(page);
    expect(numberOfFeatures).to.be.above(0);
  });

  describe('Create feature', async () => {
    it('should go to add new feature page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewFeaturePage', baseContext);

      await boFeaturesPage.goToAddFeaturePage(page);

      const pageTitle = await boFeaturesCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boFeaturesCreatePage.createPageTitle);
    });

    it('should create feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewFeature', baseContext);

      const textResult = await boFeaturesCreatePage.setFeature(page, createFeatureData);
      expect(textResult).to.contains(boFeaturesPage.successfulCreationMessage);
    });
  });

  describe('View feature', async () => {
    it('should filter list of features by the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterFeature', baseContext);

      await boFeaturesPage.filterTable(page, 'name', createFeatureData.name);

      const textColumn = await boFeaturesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createFeatureData.name);
    });

    it('should view feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewFeature', baseContext);

      await boFeaturesPage.viewFeature(page, 1);

      const pageTitle = await boFeaturesViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${createFeatureData.name} • ${global.INSTALL.SHOP_NAME}`);
    });
  });

  describe('Create value', async () => {
    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewValuePage', baseContext);

      await boFeaturesViewPage.goToAddNewValuePage(page);

      const pageTitle = await boFeaturesValueCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boFeaturesValueCreatePage.createPageTitle);
    });

    it('should create value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewValue', baseContext);

      const textResult = await boFeaturesValueCreatePage.addEditValue(page, createFeatureValueData, true);
      expect(textResult).to.contains(boFeaturesValueCreatePage.successfulCreationMessage);
    });

    it('should create a second value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSecondValue', baseContext);

      const textResult = await boFeaturesValueCreatePage.addEditValue(page, createSecondFeatureValueData, false);
      expect(textResult).to.contains(boFeaturesViewPage.successfulCreationMessage);
    });
  });

  describe('View value', async () => {
    it('should view feature and check number of values after creation', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewFeature1', baseContext);

      const pageTitle = await boFeaturesViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${createFeatureData.name} • ${global.INSTALL.SHOP_NAME}`);

      const numberOfValuesAfterCreation = await boFeaturesViewPage.resetAndGetNumberOfLines(page);
      expect(numberOfValuesAfterCreation).to.equal(numberOfValues + 2);
    });
  });

  describe('Update value', async () => {
    it('should go to edit the second value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditValuePage', baseContext);

      await boFeaturesViewPage.goToEditValuePage(page, 2);

      const pageTitle = await boFeaturesValueCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boFeaturesValueCreatePage.editPageTitle);
    });

    it('should update the second value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editValue', baseContext);

      const textResult = await boFeaturesValueCreatePage.addEditValue(page, editSecondFeatureValueData, false);
      expect(textResult).to.contains(boFeaturesViewPage.successfulUpdateMessage);
    });
  });

  describe('Update feature', async () => {
    it('should click on \'Back to the list\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToTheListForUpdate', baseContext);

      await boFeaturesViewPage.clickOnBackToTheListButton(page);

      const pageTitle = await boFeaturesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boFeaturesPage.pageTitle);
    });

    it('should filter list of features by the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterFeatureForUpdate', baseContext);

      await boFeaturesPage.filterTable(page, 'name', createFeatureData.name);

      const textColumn = await boFeaturesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createFeatureData.name);
    });

    it('should edit the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editFeature', baseContext);

      await boFeaturesPage.clickOnEditFeature(page, 1);

      const textResult = await boFeaturesCreatePage.setFeature(page, editFeatureData);
      expect(textResult).to.contains(boFeaturesCreatePage.successfulUpdateMessage);
    });
  });

  describe('Delete value', async () => {
    it('should view feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewFeature2', baseContext);

      await boFeaturesPage.viewFeature(page, 1);

      const pageTitle = await boFeaturesViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${editFeatureData.name} • ${global.INSTALL.SHOP_NAME}`);
    });

    it('should delete the second value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteValue', baseContext);

      const textResult = await boFeaturesViewPage.deleteValue(page, 2);
      expect(textResult).to.contains(boFeaturesViewPage.successfulDeleteMessage);
    });
  });

  describe('Delete feature', async () => {
    it('should click on \'Back to the list\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToTheListForDelete', baseContext);

      await boFeaturesViewPage.clickOnBackToTheListButton(page);

      const pageTitle = await boFeaturesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boFeaturesPage.pageTitle);
    });

    it('should filter list of features by the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterFeatureForDelete', baseContext);

      await boFeaturesPage.filterTable(page, 'name', editFeatureData.name);

      const textColumn = await boFeaturesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(editFeatureData.name);
    });

    it('should delete the created feature', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFeature', baseContext);

      const textResult = await boFeaturesPage.deleteFeature(page, 1);
      expect(textResult).to.contains(boFeaturesPage.successfulDeleteMessage);
    });
  });
});
