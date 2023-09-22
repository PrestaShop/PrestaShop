// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import attributesPage from '@pages/BO/catalog/attributes';
import viewAttributePage from '@pages/BO/catalog/attributes/view';

// Import data
import Attributes from '@data/demo/attributes';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_attributes_changePosition';

/*
Scenario:
- Go to attributes page
- Sort by position ASC
- Change first attribute position to 3
- Reset attribute position
- Filter by attribute Color
- View attribute
- Change first value position to 3
- Reset value position
 */
describe('BO - Catalog - Attributes & Features : Change attributes & values position', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

    const pageTitle = await attributesPage.getPageTitle(page);
    expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  describe('Change attribute position', async () => {
    // Should reset filters and sort by position before changing position
    it('should reset all filters and get number of attributes in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAttributesFilters', baseContext);

      const numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(2);
    });

    it('should sort by \'position\' \'asc\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sortByPosition', baseContext);

      const nonSortedTable = await attributesPage.getAllRowsColumnContent(page, 'a!position');

      await attributesPage.sortTable(page, 'a!position', 'up');

      const sortedTable = await attributesPage.getAllRowsColumnContent(page, 'a!position');

      const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
      const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

      const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);
      expect(sortedTableFloat).to.deep.equal(expectedResult);
    });

    it('should change first attribute position to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeAttributePosition', baseContext);

      // Get first row attribute name
      const firstRowAttributeName = await attributesPage.getTextColumn(page, 1, 'b!name');

      // Change position and check successful message
      const textResult = await attributesPage.changePosition(page, 1, 3);
      expect(textResult, 'Unable to change position').to.contains(attributesPage.successfulUpdateMessage);

      // Get third row attribute name and check if is equal the first row attribute name before changing position
      const thirdRowAttributeName = await attributesPage.getTextColumn(page, 3, 'b!name');
      expect(thirdRowAttributeName, 'Changing position was done wrongly').to.equal(firstRowAttributeName);
    });

    it('should reset third attribute position to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAttributePosition', baseContext);

      // Get third row attribute name
      const thirdRowAttributeName = await attributesPage.getTextColumn(page, 3, 'b!name');

      // Change position and check successful message
      const textResult = await attributesPage.changePosition(page, 3, 1);
      expect(textResult, 'Unable to change position').to.contains(attributesPage.successfulUpdateMessage);

      // Get first row attribute name and check if is equal the first row attribute name before changing position
      const firstRowAttributeName = await attributesPage.getTextColumn(page, 1, 'b!name');
      expect(firstRowAttributeName, 'Changing position was done wrongly').to.equal(thirdRowAttributeName);
    });
  });

  describe('Change value position', async () => {
    it(`should filter list of attributes by Name ${Attributes.size.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAttributes', baseContext);

      await attributesPage.filterTable(page, 'b!name', Attributes.size.name);

      const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains(Attributes.size.name);
    });

    it('should view attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewAttribute', baseContext);

      await attributesPage.viewAttribute(page, 1);

      const pageTitle = await viewAttributePage.getPageTitle(page);
      expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} ${Attributes.size.name}`);
    });

    // Should reset filters before changing position
    it('should reset all filters and get number of values in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetValueFilters', baseContext);

      const numberOfValues = await viewAttributePage.resetAndGetNumberOfLines(page);
      expect(numberOfValues).to.be.above(2);
    });

    it('should change first value position to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeValuePosition', baseContext);

      // Get first row attribute name
      const firstRowValueName = await viewAttributePage.getTextColumn(page, 1, 'b!name');

      // Change position and check successful message
      const textResult = await viewAttributePage.changePosition(page, 1, 3);
      expect(textResult, 'Unable to change position').to.contains(attributesPage.successfulUpdateMessage);

      // Get third row attribute name and check if is equal the first row attribute name before changing position
      const thirdRowValueName = await viewAttributePage.getTextColumn(page, 3, 'b!name');
      expect(thirdRowValueName, 'Changing position was done wrongly').to.equal(firstRowValueName);
    });

    it('should reset third value position to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetValuePosition', baseContext);

      // Get third row attribute name
      const thirdRowValueName = await viewAttributePage.getTextColumn(page, 3, 'b!name');

      // Change position and check successful message
      const textResult = await viewAttributePage.changePosition(page, 3, 1);
      expect(textResult, 'Unable to change position').to.contains(attributesPage.successfulUpdateMessage);

      // Get first row attribute name and check if is equal the first row attribute name before changing position
      const firstRowValueName = await viewAttributePage.getTextColumn(page, 1, 'b!name');
      expect(firstRowValueName, 'Changing position was done wrongly').to.equal(thirdRowValueName);
    });
  });
});
