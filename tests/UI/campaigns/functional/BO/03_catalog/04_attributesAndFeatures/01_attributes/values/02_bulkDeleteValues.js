require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const viewAttributePage = require('@pages/BO/catalog/attributes/view');
const addValuePage = require('@pages/BO/catalog/attributes/addValue');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_attributes_values_bulkDeleteValues';

// Import expect from chai
const {expect} = require('chai');

// Create data
const {Value} = require('@data/faker/attributeAndValue');

const valuesToCreate = [
  new Value({attributeName: 'Color', value: 'toDelete1'}),
  new Value({attributeName: 'Color', value: 'toDelete2'}),
];

// Browser and tab
let browserContext;
let page;

let numberOfValues = 0;

describe('Bulk delete values', async () => {
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

  valuesToCreate.forEach((valueToCreate, index) => {
    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateValuePage', baseContext);

      await attributesPage.goToAddNewValuePage(page);

      const pageTitle = await addValuePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addValuePage.createPageTitle);
    });

    it('should create new value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createNewAttribute${index}`, baseContext);

      const textResult = await addValuePage.addEditValue(page, valueToCreate);
      await expect(textResult).to.contains(attributesPage.successfulCreationMessage);
    });
  });

  it('should filter list of attributes', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDeleteAttributes', baseContext);

    await attributesPage.filterTable(page, 'b!name', 'Color');

    const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
    await expect(textColumn).to.contains('Color');
  });

  it('should view attribute', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedAttribute', baseContext);

    await attributesPage.viewAttribute(page, 1);

    const pageTitle = await viewAttributePage.getPageTitle(page);
    await expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} Color`);

    numberOfValues = await viewAttributePage.resetAndGetNumberOfLines(page);
    await expect(numberOfValues).to.be.above(0);
  });

  it('should filter by value name \'toDelete\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

    await viewAttributePage.filterTable(page, 'b!name', 'toDelete');

    const numberOfValuesAfterFilter = await viewAttributePage.getNumberOfElementInGrid(page);
    await expect(numberOfValuesAfterFilter).to.be.at.most(numberOfValues);
  });

  it('should delete values with Bulk Actions and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAttributes', baseContext);

    const deleteTextResult = await viewAttributePage.bulkDeleteValues(page);
    await expect(deleteTextResult).to.be.contains(viewAttributePage.successfulMultiDeleteMessage);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    const numberOfValuesAfterReset = await viewAttributePage.resetAndGetNumberOfLines(page);
    await expect(numberOfValuesAfterReset).to.equal(numberOfValues);
  });
});
