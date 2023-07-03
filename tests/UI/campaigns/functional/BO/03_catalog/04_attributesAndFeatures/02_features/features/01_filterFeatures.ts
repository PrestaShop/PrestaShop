// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import attributesPage from '@pages/BO/catalog/attributes';
import featuresPage from '@pages/BO/catalog/features';

// Import data
import Features from '@data/demo/features';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_features_filterFeatures';

// Filter features table by id, name and position
describe('BO - Catalog - Attributes & Features : Filter features table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfFeatures: number = 0;

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

  it('should go to \'Catalog > Attributes & features\' page', async function () {
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

  it('should go to features page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await attributesPage.goToFeaturesPage(page);

    const pageTitle = await featuresPage.getPageTitle(page);
    await expect(pageTitle).to.contains(featuresPage.pageTitle);
  });

  it('should reset all filters and get number of features in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfFeatures = await featuresPage.resetAndGetNumberOfLines(page);
    await expect(numberOfFeatures).to.be.above(0);
  });

  describe('Filter features', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterBy: 'id_feature', filterValue: Features.composition.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterBy: 'name', filterValue: Features.composition.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterPosition',
          filterBy: 'position',
          filterValue: Features.composition.position - 1,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await featuresPage.filterTable(
          page,
          test.args.filterBy,
          typeof test.args.filterValue === 'number' ? test.args.filterValue.toString() : test.args.filterValue,
        );

        const numberOfFeaturesAfterFilter = await featuresPage.getNumberOfElementInGrid(page);
        await expect(numberOfFeaturesAfterFilter).to.be.at.most(numberOfFeatures);

        const textColumn = await featuresPage.getTextColumn(
          page,
          1,
          test.args.filterBy,
        );

        if (typeof test.args.filterValue === 'number') {
          await expect(textColumn).to.contains(test.args.filterValue + 1);
        } else {
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfFeaturesAfterReset = await featuresPage.resetAndGetNumberOfLines(page);
        await expect(numberOfFeaturesAfterReset).to.equal(numberOfFeatures);
      });
    });
  });
});
