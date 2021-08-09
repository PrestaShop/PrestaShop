require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const featuresPage = require('@pages/BO/catalog/features');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_features_features_changePosition';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

/*
Go To features page
View first feature
Change first feature position to 3
Reset value position
 */
describe('BO - Catalog - Attributes & Features : Change feature position', async () => {
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
  });

  it('should sort by \'position\' \'asc\' And check result', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sortByPosition', baseContext);

    let nonSortedTable = await featuresPage.getAllRowsColumnContent(page, 'a!position');

    await featuresPage.sortTable(page, 'a!position', 'up');

    let sortedTable = await featuresPage.getAllRowsColumnContent(page, 'a!position');

    nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
    sortedTable = await sortedTable.map(text => parseFloat(text));

    const expectedResult = await featuresPage.sortArray(nonSortedTable, true);

    await expect(sortedTable).to.deep.equal(expectedResult);
  });

  it('should change first feature position to 2', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeFeaturePosition', baseContext);

    // Get first row feature name
    const firstRowFeatureName = await featuresPage.getTextColumn(page, 1, 'b!name');

    // Change position and check successful message
    const textResult = await featuresPage.changePosition(page, 1, 2);
    await expect(textResult, 'Unable to change position').to.contains(featuresPage.successfulUpdateMessage);

    // Get second row feature name and check if is equal the first row feature name before changing position
    const secondRowFeatureName = await featuresPage.getTextColumn(page, 2, 'b!name');
    await expect(secondRowFeatureName, 'Changing position was done wrongly').to.equal(firstRowFeatureName);
  });

  it('should reset second feature position to 1', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFeaturePosition', baseContext);

    // Get third row feature name
    const secondRowFeatureName = await featuresPage.getTextColumn(page, 2, 'b!name');

    // Change position and check successful message
    const textResult = await featuresPage.changePosition(page, 2, 1);
    await expect(textResult, 'Unable to change position').to.contains(featuresPage.successfulUpdateMessage);

    // Get first row feature name and check if is equal the first row feature name before changing position
    const firstRowFeatureName = await featuresPage.getTextColumn(page, 1, 'b!name');
    await expect(firstRowFeatureName, 'Changing position was done wrongly').to.equal(secondRowFeatureName);
  });
});
