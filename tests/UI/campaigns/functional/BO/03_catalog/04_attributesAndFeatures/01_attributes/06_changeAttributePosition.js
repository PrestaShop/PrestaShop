require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const viewAttributePage = require('@pages/BO/catalog/attributes/view');

// Import data
const {Attributes} = require('@data/demo/attributes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_attributes_changeAttributePosition';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

/*
Go To attributes page
Change first attribute position to 3
Reset attribute position
View first attribute
Change first value position to 3
Reset value position
 */
describe('Change attribute and value position', async () => {
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

  it('should go to attributes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.attributesAndFeaturesLink,
    );

    const pageTitle = await attributesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(attributesPage.pageTitle);
  });


  describe('Change attribute position', async () => {
    // Should reset filters and sort by position before changing position
    it('should reset all filters and get number of attributes in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAttributesFilters', baseContext);

      const numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfAttributes).to.be.above(2);
    });

    it('should sort by \'position\' \'asc\' And check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sortByPosition', baseContext);

      let nonSortedTable = await attributesPage.getAllRowsColumnContent(page, 'a!position');

      await attributesPage.sortTable(page, 'a!position', 'up');

      let sortedTable = await attributesPage.getAllRowsColumnContent(page, 'a!position');

      nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
      sortedTable = await sortedTable.map(text => parseFloat(text));

      const expectedResult = await attributesPage.sortArray(nonSortedTable, true);

      await expect(sortedTable).to.deep.equal(expectedResult);
    });

    it('should change first attribute position to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeAttributePosition', baseContext);

      // Get first row attribute name
      const firstRowAttributeName = await attributesPage.getTextColumn(page, 1, 'b!name');

      // Change position and check successful message
      const textResult = await attributesPage.changePosition(page, 1, 3);
      await expect(textResult, 'Unable to change position').to.contains(attributesPage.successfulUpdateMessage);

      // Get third row attribute name and check if is equal the first row attribute name before changing position
      const thirdRowAttributeName = await attributesPage.getTextColumn(page, 3, 'b!name');
      await expect(thirdRowAttributeName, 'Changing position was done wrongly').to.equal(firstRowAttributeName);
    });

    it('should reset third attribute position to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAttributePosition', baseContext);

      // Get third row attribute name
      const thirdRowAttributeName = await attributesPage.getTextColumn(page, 3, 'b!name');

      // Change position and check successful message
      const textResult = await attributesPage.changePosition(page, 3, 1);
      await expect(textResult, 'Unable to change position').to.contains(attributesPage.successfulUpdateMessage);

      // Get first row attribute name and check if is equal the first row attribute name before changing position
      const firstRowAttributeName = await attributesPage.getTextColumn(page, 1, 'b!name');
      await expect(firstRowAttributeName, 'Changing position was done wrongly').to.equal(thirdRowAttributeName);
    });
  });

  describe('Change value position', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAttributes', baseContext);

      await attributesPage.filterTable(
        page,
        'b!name',
        Attributes.size.name,
      );

      const textColumn = await attributesPage.getTextColumn(
        page,
        1,
        'b!name',
      );

      await expect(textColumn).to.contains(Attributes.size.name);
    });

    it('should view attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewAttribute', baseContext);

      await attributesPage.viewAttribute(page, 1);

      const pageTitle = await viewAttributePage.getPageTitle(page);
      await expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} ${Attributes.size.name}`);
    });

    // Should reset filters before changing position
    it('should reset all filters and get number of attributes in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetValueFilters', baseContext);

      const numberOfValues = await viewAttributePage.resetAndGetNumberOfLines(page);
      await expect(numberOfValues).to.be.above(2);
    });

    it('should change first value position to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeValuePosition', baseContext);

      // Get first row attribute name
      const firstRowValueName = await viewAttributePage.getTextColumn(page, 1, 'b!name');

      // Change position and check successful message
      const textResult = await viewAttributePage.changePosition(page, 1, 3);
      await expect(textResult, 'Unable to change position').to.contains(attributesPage.successfulUpdateMessage);

      // Get third row attribute name and check if is equal the first row attribute name before changing position
      const thirdRowValueName = await viewAttributePage.getTextColumn(page, 3, 'b!name');
      await expect(thirdRowValueName, 'Changing position was done wrongly').to.equal(firstRowValueName);
    });

    it('should reset third value position to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetValuePosition', baseContext);

      // Get third row attribute name
      const thirdRowValueName = await viewAttributePage.getTextColumn(page, 3, 'b!name');

      // Change position and check successful message
      const textResult = await viewAttributePage.changePosition(page, 3, 1);
      await expect(textResult, 'Unable to change position').to.contains(attributesPage.successfulUpdateMessage);

      // Get first row attribute name and check if is equal the first row attribute name before changing position
      const firstRowValueName = await viewAttributePage.getTextColumn(page, 1, 'b!name');
      await expect(firstRowValueName, 'Changing position was done wrongly').to.equal(thirdRowValueName);
    });
  });
});
