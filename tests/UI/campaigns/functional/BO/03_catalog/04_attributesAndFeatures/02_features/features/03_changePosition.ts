// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import attributesPage from '@pages/BO/catalog/attributes';
import featuresPage from '@pages/BO/catalog/features';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_features_changePosition';

/*
Go To features page
View first feature
Change first feature position to 3
Reset value position
 */
describe('BO - Catalog - Attributes & Features : Change feature position', async () => {
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

    const nonSortedTable = await featuresPage.getAllRowsColumnContent(page, 'position', 'id_feature');

    await featuresPage.sortTable(page, 'position', 'asc');

    const sortedTable = await featuresPage.getAllRowsColumnContent(page, 'position', 'position');

    const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
    const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

    const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

    await expect(sortedTableFloat).to.deep.equal(expectedResult);
  });

  it('should change first feature position to 2', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeFeaturePosition', baseContext);

    // Get first row feature name
    const firstRowFeatureName = await featuresPage.getTextColumn(page, 1, 'name', 'position');

    // Change position and check successful message
    const textResult = await featuresPage.changePosition(page, 1, 2);
    await expect(textResult, 'Unable to change position').to.contains(featuresPage.successfulUpdateMessage);

    // Get second row feature name and check if is equal the first row feature name before changing position
    const secondRowFeatureName = await featuresPage.getTextColumn(page, 2, 'name', 'position');
    await expect(secondRowFeatureName, 'Changing position was done wrongly').to.equal(firstRowFeatureName);
  });

  it('should reset second feature position to 1', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFeaturePosition', baseContext);

    // Close alert
    await featuresPage.closeAlertParagraph(page);

    // Get third row feature name
    const secondRowFeatureName = await featuresPage.getTextColumn(page, 2, 'name', 'position');

    // Change position and check successful message
    const textResult = await featuresPage.changePosition(page, 2, 1);
    await expect(textResult, 'Unable to change position').to.contains(featuresPage.successfulUpdateMessage);

    // Get first row feature name and check if is equal the first row feature name before changing position
    const firstRowFeatureName = await featuresPage.getTextColumn(page, 1, 'name', 'position');
    await expect(firstRowFeatureName, 'Changing position was done wrongly').to.equal(secondRowFeatureName);
  });
});
