require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const featuresPage = require('@pages/BO/catalog/features');

// Import data
const {Features} = require('@data/demo/features');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_catalog_attributesAndFeatures_features_filterFeatures';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfFeatures = 0;

// Filter features table by id, name and position
describe('BO - Catalog - Attributes & Features : Filter features table', async () => {
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
          testIdentifier: 'filterId', filterBy: 'id_feature', filterValue: Features.composition.id,
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterBy: 'b!name', filterValue: Features.composition.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterPosition', filterBy: 'a!position', filterValue: (Features.composition.position - 1),
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await featuresPage.filterTable(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfFeaturesAfterFilter = await featuresPage.getNumberOfElementInGrid(page);
        await expect(numberOfFeaturesAfterFilter).to.be.at.most(numberOfFeatures);

        const textColumn = await featuresPage.getTextColumn(
          page,
          1,
          test.args.filterBy,
        );

        if (test.args.filterBy === 'a!position') {
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
