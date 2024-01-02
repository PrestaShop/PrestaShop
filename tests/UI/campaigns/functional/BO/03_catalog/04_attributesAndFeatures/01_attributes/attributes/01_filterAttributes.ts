// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import attributesPage from '@pages/BO/catalog/attributes';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Attributes from '@data/demo/attributes';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_attributes_attributes_filterAttributes';

/*
Go to Attributes & Features page
Filter attributes table by ID, Name and Position
 */
describe('BO - Catalog - Attributes & Features : Filter attributes table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAttributes: number = 0;

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

  it('should reset all filters and get number of attributes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
    expect(numberOfAttributes).to.be.above(0);
  });

  describe('Filter attributes', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterBy: 'id_attribute_group', filterValue: Attributes.size.id.toString(),
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
          typeof test.args.filterValue === 'number' ? test.args.filterValue.toString() : test.args.filterValue,
        );

        const numberOfAttributesAfterFilter = await attributesPage.getNumberOfElementInGrid(page);
        expect(numberOfAttributesAfterFilter).to.be.at.most(numberOfAttributes);

        const textColumn = await attributesPage.getTextColumn(page, 1, test.args.filterBy);

        if (typeof test.args.filterValue === 'number') {
          expect(textColumn).to.contains(test.args.filterValue + 1);
        } else {
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfAttributesAfterReset = await attributesPage.resetAndGetNumberOfLines(page);
        expect(numberOfAttributesAfterReset).to.equal(numberOfAttributes);
      });
    });
  });
});
