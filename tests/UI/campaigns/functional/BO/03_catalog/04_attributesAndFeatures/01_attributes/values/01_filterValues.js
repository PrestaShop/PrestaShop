require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const viewAttributePage = require('@pages/BO/catalog/attributes/view');

// Import data
const {Attributes} = require('@data/demo/attributes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_attributes_values_filterValues';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfValues = 0;

/*
Go to Attributes & Features page
Go to view attribute 'Color' page
Filter values table by ID, Name and Position
 */
describe('BO - Catalog - Attributes & Features : Filter attribute values table', async () => {
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

  it('should filter attributes table by name \'Color\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterAttributes', baseContext);

    await attributesPage.filterTable(page, 'b!name', Attributes.color.name);

    const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
    await expect(textColumn).to.contains(Attributes.color.name);
  });

  it('should view attribute', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewAttribute', baseContext);

    await attributesPage.viewAttribute(page, 1);

    const pageTitle = await viewAttributePage.getPageTitle(page);
    await expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} ${Attributes.color.name}`);
  });

  it('should reset all filters and get number of values in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfValues = await viewAttributePage.resetAndGetNumberOfLines(page);
    await expect(numberOfValues).to.be.above(0);
  });

  describe('Filter values table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterBy: 'id_attribute',
            filterValue: Attributes.color.values.pink.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterBy: 'b!name',
            filterValue: Attributes.color.values.white.value,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterColor',
            filterBy: 'a!color',
            filterValue: Attributes.color.values.camel.color,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterPosition',
            filterBy: 'a!position',
            filterValue: (Attributes.color.values.green.position - 1),
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await viewAttributePage.filterTable(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfValuesAfterFilter = await viewAttributePage.getNumberOfElementInGrid(page);
        await expect(numberOfValuesAfterFilter).to.be.at.most(numberOfValues);

        const textColumn = await viewAttributePage.getTextColumn(page, 1, test.args.filterBy);

        if (test.args.filterBy === 'a!position') {
          await expect(textColumn).to.contains(test.args.filterValue + 1);
        } else {
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfValuesAfterReset = await viewAttributePage.resetAndGetNumberOfLines(page);
        await expect(numberOfValuesAfterReset).to.equal(numberOfValues);
      });
    });
  });
});
