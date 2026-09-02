import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boAttributesCreatePage,
  boAttributesValueCreatePage,
  boAttributesViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAttribute,
  FakerAttributeValue,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  let attributeId: number = 0;

  const createAttributeData: FakerAttribute = new FakerAttribute();
  const updateAttributeData: FakerAttribute = new FakerAttribute();
  const valuesToCreate: FakerAttributeValue[] = [
    new FakerAttributeValue({attributeName: createAttributeData.name}),
    new FakerAttributeValue({attributeName: createAttributeData.name}),
  ];
  const updateValueData: FakerAttributeValue = new FakerAttributeValue({attributeName: updateAttributeData.name});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create needed files
    await Promise.all([
      utilsFile.createFile('./', valuesToCreate[0].textureFileName, 'text'),
      utilsFile.createFile('./', valuesToCreate[1].textureFileName, 'text'),
      utilsFile.createFile('./', updateValueData.textureFileName, 'text'),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      utilsFile.deleteFile(valuesToCreate[0].textureFileName),
      utilsFile.deleteFile(valuesToCreate[1].textureFileName),
      utilsFile.deleteFile(updateValueData.textureFileName),
    ]);
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

  it('should reset all filters and get number of attributes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfAttributes = await boAttributesPage.resetAndGetNumberOfLines(page);
    expect(numberOfAttributes).to.be.above(0);
  });

  describe('Create attribute', async () => {
    it('should go to add new attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAttributePage', baseContext);

      await boAttributesPage.goToAddAttributePage(page);

      const pageTitle = await boAttributesCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesCreatePage.createPageTitle);
    });

    it('should create new attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAttribute', baseContext);

      const textResult = await boAttributesCreatePage.addEditAttribute(page, createAttributeData);
      expect(textResult).to.contains(boAttributesPage.successfulCreationMessage);

      const numberOfAttributesAfterCreation = await boAttributesPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + 1);
    });
  });

  describe('View attribute', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAttribute', baseContext);

      await boAttributesPage.filterTable(page, 'name', createAttributeData.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeData.name);

      attributeId = parseInt(await boAttributesPage.getTextColumn(page, 1, 'id_attribute_group'), 10);
      expect(attributeId).to.be.gt(0);
    });

    it('should view attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedAttribute', baseContext);

      await boAttributesPage.viewAttribute(page, 1);

      const pageTitle = await boAttributesViewPage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(createAttributeData.name));
    });
  });

  describe('Create 2 values', async () => {
    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateValuePage', baseContext);

      await boAttributesViewPage.goToAddNewValuePage(page);

      const pageTitle = await boAttributesValueCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesValueCreatePage.createPageTitle);
    });

    valuesToCreate.forEach((valueToCreate: FakerAttributeValue, index: number) => {
      it(`should create value n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createValue${index}`, baseContext);

        valueToCreate.setAttributeId(attributeId);
        const textResult = await boAttributesValueCreatePage.addEditValue(page, valueToCreate, index === 0);
        expect(textResult).to.contains(boAttributesViewPage.successfulCreationMessage);
      });
    });

    it('return to the Attributes page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnAttributesPage', baseContext);

      await boAttributesViewPage.backToAttributesList(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });
  });

  describe('Update attribute', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAttribute', baseContext);

      await boAttributesPage.resetFilter(page);
      await boAttributesPage.filterTable(page, 'name', createAttributeData.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeData.name);
    });

    it('should go to edit attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAttributePage', baseContext);

      await boAttributesPage.goToEditAttributePage(page, 1);

      const pageTitle = await boAttributesCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesCreatePage.editPageTitle(createAttributeData.name));
    });

    it('should update attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAttribute', baseContext);

      const textResult = await boAttributesCreatePage.addEditAttribute(page, updateAttributeData);
      expect(textResult).to.contains(boAttributesPage.successfulUpdateMessage);
    });
  });

  describe('View updated attribute', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdateAttribute', baseContext);

      await boAttributesPage.filterTable(page, 'name', updateAttributeData.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(updateAttributeData.name);
    });

    it('should view updated attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedAttribute', baseContext);

      await boAttributesPage.viewAttribute(page, 1);

      const pageTitle = await boAttributesViewPage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(updateAttributeData.name));

      const numberOfValues = await boAttributesViewPage.getNumberOfElementInGrid(page);
      expect(numberOfValues).to.equal(2);
    });
  });

  describe('Update first value', async () => {
    it('should filter values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterValuesToUpdate', baseContext);

      await boAttributesViewPage.filterTable(page, 'name', valuesToCreate[0].value);

      const textColumn = await boAttributesViewPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(valuesToCreate[0].value);
    });

    it('should go to edit value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditValuePage', baseContext);

      await boAttributesViewPage.goToEditValuePage(page, 1);

      const pageTitle = await boAttributesValueCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesValueCreatePage.editPageTitle(valuesToCreate[0].value));
    });

    it('should update value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateValue', baseContext);

      updateValueData.setAttributeId(attributeId);
      const textResult = await boAttributesValueCreatePage.addEditValue(page, updateValueData);
      expect(textResult).to.contains(boAttributesValueCreatePage.successfulUpdateMessage);
    });
  });

  describe('Delete second value', async () => {
    it('should filter values', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterValuesToDelete', baseContext);

      await boAttributesViewPage.filterTable(page, 'name', valuesToCreate[1].value);

      const textColumn = await boAttributesViewPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(valuesToCreate[1].value);
    });

    it('should delete value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteValue', baseContext);

      const textResult = await boAttributesViewPage.deleteValue(page, 1);
      expect(textResult).to.contains(boAttributesViewPage.successfulDeleteMessage);

      const numberOfValues = await boAttributesViewPage.resetAndGetNumberOfLines(page);
      expect(numberOfValues).to.equal(1);
    });
  });

  describe('Delete attribute', async () => {
    it('should go to attributes page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToAttributesPageToDelete', baseContext);

      await boAttributesViewPage.backToAttributesList(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should filter attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAttributesToDelete', baseContext);

      await boAttributesPage.resetFilter(page);
      await boAttributesPage.filterTable(page, 'name', updateAttributeData.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(updateAttributeData.name);
    });

    it('should delete attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAttribute', baseContext);

      const textResult = await boAttributesPage.deleteAttribute(page, 1);
      expect(textResult).to.contains(boAttributesPage.successfulDeleteMessage);

      const numberOfAttributesAfterDelete = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes);
    });
  });
});
