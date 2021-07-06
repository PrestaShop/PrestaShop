require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');

// Import data
const {Attributes} = require('@data/demo/attributes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_attributes_attributes_filterAttributes';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfAttributes = 0;

/*
Go to Attributes & Features page
Filter attributes table by ID, Name and Position
 */
describe('BO - Catalog - Attributes & Features : Filter attributes table', async () => {
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

  it('should reset all filters and get number of attributes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfAttributes).to.be.above(0);
  });

  describe('Filter attributes', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterBy: 'id_attribute_group', filterValue: Attributes.size.id,
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterBy: 'b!name', filterValue: Attributes.color.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterPosition', filterBy: 'a!position', filterValue: (Attributes.paperType.position - 1),
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await attributesPage.filterTable(
          page,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfAttributesAfterFilter = await attributesPage.getNumberOfElementInGrid(page);
        await expect(numberOfAttributesAfterFilter).to.be.at.most(numberOfAttributes);

        const textColumn = await attributesPage.getTextColumn(page, 1, test.args.filterBy);

        if (test.args.filterBy === 'a!position') {
          await expect(textColumn).to.contains(test.args.filterValue + 1);
        } else {
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfAttributesAfterReset = await attributesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfAttributesAfterReset).to.equal(numberOfAttributes);
      });
    });
  });
});
