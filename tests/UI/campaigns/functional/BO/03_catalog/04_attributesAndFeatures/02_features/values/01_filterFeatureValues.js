require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const featuresPage = require('@pages/BO/catalog/features');
const viewFeaturePage = require('@pages/BO/catalog/features/view');

// Import data
const {Features} = require('@data/demo/features');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_catalog_attributesAndFeatures_features_filterFeatureValues';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfFeaturesValues = 0;

// Filter feature values by id and name
describe('BO - Catalog - Attributes & Features : Filter feature values table', async () => {
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

  it('should filter by name \'Composition\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterFeatures', baseContext);

    await featuresPage.resetFilter(page);
    await featuresPage.filterTable(page, 'b!name', Features.composition.name);

    const textColumn = await featuresPage.getTextColumn(page, 1, 'b!name');
    await expect(textColumn).to.contains(Features.composition.name);
  });

  it('should view feature', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewFeature', baseContext);

    await featuresPage.viewFeature(page, 1);

    const pageTitle = await viewFeaturePage.getPageTitle(page);
    await expect(pageTitle).to.contains(`${viewFeaturePage.pageTitle} ${Features.composition.name}`);
  });

  it('should reset all filters and get number of features in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfFeaturesValues = await viewFeaturePage.resetAndGetNumberOfLines(page);
    await expect(numberOfFeaturesValues).to.be.above(0);
  });

  describe('Filter feature values', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterBy: 'id_feature_value',
            filterValue: Features.composition.values.polyester.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterValue',
            filterBy: 'value',
            filterValue: Features.composition.values.polyester.value,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await viewFeaturePage.filterTable(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfFeaturesValuesAfterFilter = await viewFeaturePage.getNumberOfElementInGrid(page);
        await expect(numberOfFeaturesValuesAfterFilter).to.be.at.most(numberOfFeaturesValues);

        const textColumn = await viewFeaturePage.getTextColumn(page, 1, test.args.filterBy);
        await expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfFeaturesValuesAfterReset = await viewFeaturePage.resetAndGetNumberOfLines(page);
        await expect(numberOfFeaturesValuesAfterReset).to.equal(numberOfFeaturesValues);
      });
    });
  });
});
