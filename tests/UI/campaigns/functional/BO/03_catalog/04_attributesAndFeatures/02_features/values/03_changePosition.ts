import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boDashboardPage,
  boFeaturesPage,
  boFeaturesViewPage,
  boLoginPage,
  type BrowserContext,
  dataFeatures,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_values_changePosition';

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

    const pageTitle = await boAttributesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boAttributesPage.pageTitle);
  });

  it('should go to \'Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await boAttributesPage.goToFeaturesPage(page);

    const pageTitle = await boFeaturesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boFeaturesPage.pageTitle);
  });

  it('should view feature', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewFeature', baseContext);

    await boFeaturesPage.viewFeature(page, 1);

    const pageTitle = await boFeaturesViewPage.getPageTitle(page);
    expect(pageTitle).to.contains(`${dataFeatures.composition.name} • ${global.INSTALL.SHOP_NAME}`);
  });

  it('should reset all filters and get number of feature values in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    const numberOfFeaturesValues = await boFeaturesViewPage.resetAndGetNumberOfLines(page);
    expect(numberOfFeaturesValues).to.be.above(0);
  });

  it('should change first value position to 3', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'changeValuePosition', baseContext);

    // Get first row attribute name
    const firstRowValue = await boFeaturesViewPage.getTextColumn(page, 1, 'value');

    // Change position and check successful message
    const textResult = await boFeaturesViewPage.changePosition(page, 1, 3);
    expect(textResult, 'Unable to change position').to.contains(boFeaturesViewPage.successfulUpdateMessage);

    await boFeaturesViewPage.closeAlertBlock(page);

    // Get third row attribute name and check if is equal the first row attribute name before changing position
    const thirdRowValue = await boFeaturesViewPage.getTextColumn(page, 3, 'value');
    expect(thirdRowValue, 'Changing position was done wrongly').to.equal(firstRowValue);
  });

  it('should reset third value position to 1', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetValuePosition', baseContext);

    // Get third row feature value
    const thirdRowValue = await boFeaturesViewPage.getTextColumn(page, 3, 'value');

    // Change position and check successful message
    const textResult = await boFeaturesViewPage.changePosition(page, 3, 1);
    expect(textResult, 'Unable to change position').to.contains(boFeaturesViewPage.successfulUpdateMessage);

    await boFeaturesViewPage.closeAlertBlock(page);

    // Get first row feature value and check if is equal the first row feature value before changing position
    const firstRowValue = await boFeaturesViewPage.getTextColumn(page, 1, 'value');
    expect(firstRowValue, 'Changing position was done wrongly').to.equal(thirdRowValue);
  });
});
