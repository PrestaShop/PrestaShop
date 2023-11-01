// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import attributesPage from '@pages/BO/catalog/attributes';
import viewAttributePage from '@pages/BO/catalog/attributes/view';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Attributes from '@data/demo/attributes';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_attributes_values_filterValues';

/*
Go to Attributes & Features page
Go to view attribute 'Color' page
Filter values table by ID, Name and Position
 */
describe('BO - Catalog - Attributes & Features : Filter attribute values table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfValues: number = 0;

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

  it('should filter attributes table by name \'Color\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterAttributes', baseContext);

    await attributesPage.filterTable(page, 'b!name', Attributes.color.name);

    const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
    expect(textColumn).to.contains(Attributes.color.name);
  });

  it('should view attribute', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewAttribute', baseContext);

    await attributesPage.viewAttribute(page, 1);

    const pageTitle = await viewAttributePage.getPageTitle(page);
    expect(pageTitle).to.contains(`${viewAttributePage.pageTitle} ${Attributes.color.name}`);
  });

  it('should reset all filters and get number of values in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfValues = await viewAttributePage.resetAndGetNumberOfLines(page);
    expect(numberOfValues).to.be.above(0);
  });

  describe('Filter values table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterBy: 'id_attribute',
            filterValue: Attributes.color.values[13].id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterBy: 'b!name',
            filterValue: Attributes.color.values[3].value,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterColor',
            filterBy: 'a!color',
            filterValue: Attributes.color.values[7].color,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterPosition',
            filterBy: 'a!position',
            filterValue: (Attributes.color.values[10].position - 1),
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await viewAttributePage.filterTable(
          page,
          test.args.filterBy,
          typeof test.args.filterValue === 'number' ? test.args.filterValue.toString() : test.args.filterValue,
        );

        const numberOfValuesAfterFilter = await viewAttributePage.getNumberOfElementInGrid(page);
        expect(numberOfValuesAfterFilter).to.be.at.most(numberOfValues);

        const textColumn = await viewAttributePage.getTextColumn(page, 1, test.args.filterBy);

        if (typeof test.args.filterValue === 'number') {
          expect(textColumn).to.contains(test.args.filterValue + 1);
        } else {
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfValuesAfterReset = await viewAttributePage.resetAndGetNumberOfLines(page);
        expect(numberOfValuesAfterReset).to.equal(numberOfValues);
      });
    });
  });
});
