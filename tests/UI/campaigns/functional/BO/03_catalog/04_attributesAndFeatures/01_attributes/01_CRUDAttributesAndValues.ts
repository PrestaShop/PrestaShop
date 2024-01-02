// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import attributesPage from '@pages/BO/catalog/attributes';
import addAttributePage from '@pages/BO/catalog/attributes/addAttribute';
import addValuePage from '@pages/BO/catalog/attributes/addValue';
import viewAttributePage from '@pages/BO/catalog/attributes/view';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import AttributeData from '@data/faker/attribute';
import AttributeValueData from '@data/faker/attributeValue';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_attributes_CRUDAttributesAndValues';

/*
Create attribute
View attribute
Create two values
Update attribute
View updated attribute
Update first value
Delete second value
Delete attribute
 */

describe('BO - Catalog - Attributes & Features : CRUD attribute and values', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAttributes: number = 0;

  const createAttributeData: AttributeData = new AttributeData();
  const updateAttributeData: AttributeData = new AttributeData();
  const valuesToCreate: AttributeValueData[] = [
    new AttributeValueData({attributeName: createAttributeData.name}),
    new AttributeValueData({attributeName: createAttributeData.name}),
  ];
  const updateValueData: AttributeValueData = new AttributeValueData({attributeName: updateAttributeData.name});

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create needed files
    await Promise.all([
      files.createFile('./', valuesToCreate[0].textureFileName, 'text'),
      files.createFile('./', valuesToCreate[1].textureFileName, 'text'),
      files.createFile('./', updateValueData.textureFileName, 'text'),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      files.deleteFile(valuesToCreate[0].textureFileName),
      files.deleteFile(valuesToCreate[1].textureFileName),
      files.deleteFile(updateValueData.textureFileName),
    ]);
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
    expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  it('should reset all filters and get number of attributes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
    expect(numberOfAttributes).to.be.above(0);
  });

  describe('Create attribute', async () => {
    it('should go to add new attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAttributePage', baseContext);

      await attributesPage.goToAddAttributePage(page);

      const pageTitle = await addAttributePage.getPageTitle(page);
      expect(pageTitle).to.contains(addAttributePage.createPageTitle);
    });

    it('should create new attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAttribute', baseContext);

      const textResult = await addAttributePage.addEditAttribute(page, createAttributeData);
      expect(textResult).to.contains(attributesPage.successfulCreationMessage);

      const numberOfAttributesAfterCreation = await attributesPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + 1);
    });
  });

  describe('View attribute', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAttribute', baseContext);

      await attributesPage.filterTable(page, 'b!name', createAttributeData.name);

      const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(createAttributeData.name);
    });

    it('should view attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedAttribute', baseContext);

      await attributesPage.viewAttribute(page, 1);

      const pageTitle = await viewAttributePage.getPageTitle(page);
      expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} ${createAttributeData.name}`);
    });
  });

  describe('Create 2 values', async () => {
    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateValuePage', baseContext);

      await viewAttributePage.goToAddNewValuePage(page);

      const pageTitle = await addValuePage.getPageTitle(page);
      expect(pageTitle).to.contains(addValuePage.createPageTitle);
    });

    valuesToCreate.forEach((valueToCreate: AttributeValueData, index: number) => {
      it(`should create value n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createValue${index}`, baseContext);

        const textResult = await addValuePage.addEditValue(page, valueToCreate, index === 0);
        expect(textResult).to.contains(viewAttributePage.successfulCreationMessage);
      });
    });
  });

  describe('Update attribute', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAttribute', baseContext);

      await attributesPage.resetFilter(page);
      await attributesPage.filterTable(page, 'b!name', createAttributeData.name);

      const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(createAttributeData.name);
    });

    it('should go to edit attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAttributePage', baseContext);

      await attributesPage.goToEditAttributePage(page, 1);

      const pageTitle = await addAttributePage.getPageTitle(page);
      expect(pageTitle).to.contains(addAttributePage.editPageTitle);
    });

    it('should update attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAttribute', baseContext);

      const textResult = await addAttributePage.addEditAttribute(page, updateAttributeData);
      expect(textResult).to.contains(attributesPage.successfulUpdateMessage);
    });
  });

  describe('View updated attribute', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdateAttribute', baseContext);

      await attributesPage.filterTable(page, 'b!name', updateAttributeData.name);

      const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(updateAttributeData.name);
    });

    it('should view updated attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedAttribute', baseContext);

      await attributesPage.viewAttribute(page, 1);

      const pageTitle = await viewAttributePage.getPageTitle(page);
      expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} ${updateAttributeData.name}`);

      const numberOfValues = await viewAttributePage.getNumberOfElementInGrid(page);
      expect(numberOfValues).to.equal(2);
    });
  });

  describe('Update first value', async () => {
    it('should filter values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterValuesToUpdate', baseContext);

      await viewAttributePage.filterTable(page, 'b!name', valuesToCreate[0].value);

      const textColumn = await viewAttributePage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(valuesToCreate[0].value);
    });

    it('should go to edit value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditValuePage', baseContext);

      await viewAttributePage.goToEditValuePage(page, 1);

      const pageTitle = await addValuePage.getPageTitle(page);
      expect(pageTitle).to.contains(addValuePage.editPageTitle);
    });

    it('should update value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateValue', baseContext);

      const textResult = await addValuePage.addEditValue(page, updateValueData);
      expect(textResult).to.contains(addValuePage.successfulUpdateMessage);
    });
  });

  describe('Delete second value', async () => {
    it('should filter values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterValuesToDelete', baseContext);

      await viewAttributePage.filterTable(page, 'b!name', valuesToCreate[1].value);

      const textColumn = await viewAttributePage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(valuesToCreate[1].value);
    });

    it('should delete value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteValue', baseContext);

      const textResult = await viewAttributePage.deleteValue(page, 1);
      expect(textResult).to.contains(viewAttributePage.successfulDeleteMessage);

      const numberOfValues = await viewAttributePage.resetAndGetNumberOfLines(page);
      expect(numberOfValues).to.equal(1);
    });
  });

  describe('Delete attribute', async () => {
    it('should go to attributes page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToAttributesPageToDelete', baseContext);

      await viewAttributePage.backToAttributesList(page);

      const pageTitle = await attributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(attributesPage.pageTitle);
    });

    it('should filter attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAttributesToDelete', baseContext);

      await attributesPage.resetFilter(page);
      await attributesPage.filterTable(page, 'b!name', updateAttributeData.name);

      const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(updateAttributeData.name);
    });

    it('should delete attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAttribute', baseContext);

      const textResult = await attributesPage.deleteAttribute(page, 1);
      expect(textResult).to.contains(attributesPage.successfulDeleteMessage);

      const numberOfAttributesAfterDelete = await attributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes);
    });
  });
});
