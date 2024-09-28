// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import attributesPage from '@pages/BO/catalog/attributes';
import featuresPage from '@pages/BO/catalog/features';
import viewFeaturePage from '@pages/BO/catalog/features/view';

import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataFeatures,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_changePosition';

/*
Scenario:
- Go to features page
- View feature
- Change first value position to 3
- Reset value position
 */
describe('BO - Catalog - Attributes & Features : Change features values position', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Catalog > Attributes & Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.attributesAndFeaturesLink,
    );

    const pageTitle = await featuresPage.getPageTitle(page);
    expect(pageTitle).to.contains(featuresPage.pageTitle);
  });

  it('should go to \'Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await attributesPage.goToFeaturesPage(page);

    const pageTitle = await featuresPage.getPageTitle(page);
    expect(pageTitle).to.contains(featuresPage.pageTitle);
  });

  it('should view feature', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewFeature', baseContext);

    await featuresPage.viewFeature(page, 1);

    const pageTitle = await viewFeaturePage.getPageTitle(page);
    expect(pageTitle).to.contains(`${dataFeatures.composition.name} • ${global.INSTALL.SHOP_NAME}`);
  });

  it('should reset all filters and get number of feature values in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    const numberOfFeaturesValues = await viewFeaturePage.resetAndGetNumberOfLines(page);
    expect(numberOfFeaturesValues).to.be.above(0);
  });

  it('should change first value position to 3', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeValuePosition', baseContext);

    // Get first row attribute name
    const firstRowValue = await viewFeaturePage.getTextColumn(page, 1, 'value');

    // Change position and check successful message
    const textResult = await viewFeaturePage.changePosition(page, 1, 3);
    expect(textResult, 'Unable to change position').to.contains(viewFeaturePage.successfulUpdateMessage);

    await viewFeaturePage.closeAlertBlock(page);

    // Get third row attribute name and check if is equal the first row attribute name before changing position
    const thirdRowValue = await viewFeaturePage.getTextColumn(page, 3, 'value');
    expect(thirdRowValue, 'Changing position was done wrongly').to.equal(firstRowValue);
  });

  it('should reset third value position to 1', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetValuePosition', baseContext);

    // Get third row feature value
    const thirdRowValue = await viewFeaturePage.getTextColumn(page, 3, 'value');

    // Change position and check successful message
    const textResult = await viewFeaturePage.changePosition(page, 3, 1);
    expect(textResult, 'Unable to change position').to.contains(viewFeaturePage.successfulUpdateMessage);

    await viewFeaturePage.closeAlertBlock(page);

    // Get first row feature value and check if is equal the first row feature value before changing position
    const firstRowValue = await viewFeaturePage.getTextColumn(page, 1, 'value');
    expect(firstRowValue, 'Changing position was done wrongly').to.equal(thirdRowValue);
  });
});
