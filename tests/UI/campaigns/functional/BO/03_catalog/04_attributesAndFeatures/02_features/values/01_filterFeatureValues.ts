// Import utils
import testContext from '@utils/testContext';

// Import pages
import featuresPage from '@pages/BO/catalog/features';
import viewFeaturePage from '@pages/BO/catalog/features/view';

import {expect} from 'chai';
import {
  boAttributesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataFeatures,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_values_filterFeatureValues';

// Filter feature values by id and name
describe('BO - Catalog - Attributes & Features : Filter feature values table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfFeaturesValues: number = 0;

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
    await boAttributesPage.closeSfToolBar(page);

    const pageTitle = await boAttributesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boAttributesPage.pageTitle);
  });

  it('should go to Features page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await boAttributesPage.goToFeaturesPage(page);

    const pageTitle = await featuresPage.getPageTitle(page);
    expect(pageTitle).to.contains(featuresPage.pageTitle);
  });

  it('should filter by name \'Composition\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterFeatures', baseContext);

    await featuresPage.resetFilter(page);
    await featuresPage.filterTable(page, 'name', dataFeatures.composition.name);

    const textColumn = await featuresPage.getTextColumn(page, 1, 'name', 'id_feature');
    expect(textColumn).to.contains(dataFeatures.composition.name);
  });

  it('should view feature', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewFeature', baseContext);

    await featuresPage.viewFeature(page, 1);

    const pageTitle = await viewFeaturePage.getPageTitle(page);
    expect(pageTitle).to.contains(`${dataFeatures.composition.name} • ${global.INSTALL.SHOP_NAME}`);
  });

  it('should reset all filters and get number of features in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfFeaturesValues = await viewFeaturePage.resetAndGetNumberOfLines(page);
    expect(numberOfFeaturesValues).to.be.above(0);
  });

  describe('Filter feature values', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterBy: 'id_feature_value',
            filterValue: dataFeatures.composition.values[0].id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterValue',
            filterBy: 'value',
            filterValue: dataFeatures.composition.values[0].value,
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
        expect(numberOfFeaturesValuesAfterFilter).to.be.at.most(numberOfFeaturesValues);

        const textColumn = await viewFeaturePage.getTextColumn(page, 1, test.args.filterBy);
        expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfFeaturesValuesAfterReset = await viewFeaturePage.resetAndGetNumberOfLines(page);
        expect(numberOfFeaturesValuesAfterReset).to.equal(numberOfFeaturesValues);
      });
    });
  });
});
