// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import attributesPage from '@pages/BO/catalog/attributes';
import featuresPage from '@pages/BO/catalog/features';
import viewFeaturePage from '@pages/BO/catalog/features/view';

// Import data
import Features from '@data/demo/features';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_values_filterFeatureValues';

// Filter feature values by id and name
describe('BO - Catalog - Attributes & Features : Filter feature values table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfFeaturesValues: number = 0;

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
    expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  it('should go to Features page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await attributesPage.goToFeaturesPage(page);

    const pageTitle = await featuresPage.getPageTitle(page);
    expect(pageTitle).to.contains(featuresPage.pageTitle);
  });

  it('should filter by name \'Composition\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterFeatures', baseContext);

    await featuresPage.resetFilter(page);
    await featuresPage.filterTable(page, 'name', Features.composition.name);

    const textColumn = await featuresPage.getTextColumn(page, 1, 'name', 'id_feature');
    expect(textColumn).to.contains(Features.composition.name);
  });

  it('should view feature', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewFeature', baseContext);

    await featuresPage.viewFeature(page, 1);

    const pageTitle = await viewFeaturePage.getPageTitle(page);
    expect(pageTitle).to.contains(`${Features.composition.name} • ${global.INSTALL.SHOP_NAME}`);
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
            filterValue: Features.composition.values[0].id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterValue',
            filterBy: 'value',
            filterValue: Features.composition.values[0].value,
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
