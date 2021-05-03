require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const addAttributePage = require('@pages/BO/catalog/attributes/addAttribute');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_attributes_attributes_bulkDeleteAttributes';

// Import expect from chai
const {expect} = require('chai');

// Create data
const {Attribute} = require('@data/faker/attributeAndValue');

const attributesToCreate = [
  new Attribute({name: 'toDelete1'}),
  new Attribute({name: 'toDelete2'}),
];

// Browser and tab
let browserContext;
let page;

let numberOfAttributes = 0;

/*
Go to Attributes & Features page
Create 2 attributes
Delete the created attributes by bulk actions
 */
describe('Bulk delete attributes', async () => {
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

  it('should reset all filters and get number of attributes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfAttributes).to.be.above(0);
  });

  attributesToCreate.forEach((attributeToCreate, index) => {
    it('should go to add new attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddAttributePage${index}`, baseContext);

      await attributesPage.goToAddAttributePage(page);
      const pageTitle = await addAttributePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addAttributePage.createPageTitle);
    });

    it('should create new attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createNewAttribute${index}`, baseContext);

      const textResult = await addAttributePage.addEditAttribute(page, attributeToCreate);
      await expect(textResult).to.contains(attributesPage.successfulCreationMessage);

      const numberOfAttributesAfterCreation = await attributesPage.getNumberOfElementInGrid(page);
      await expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + index + 1);
    });
  });

  it('should filter list of attributes', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDeleteAttributes', baseContext);

    await attributesPage.filterTable(
      page,
      'b!name',
      'toDelete',
    );

    const textColumn = await attributesPage.getTextColumn(
      page,
      1,
      'b!name',
    );

    await expect(textColumn).to.contains('toDelete');
  });

  it('should delete attributes with Bulk Actions and check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAttributes', baseContext);

    const deleteTextResult = await attributesPage.bulkDeleteAttributes(page);
    await expect(deleteTextResult).to.be.contains(attributesPage.successfulMultiDeleteMessage);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

    const numberOfAttributesAfterReset = await attributesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfAttributesAfterReset).to.be.equal(numberOfAttributes);
  });
});
