import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boAttributesViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataAttributes,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  it('should filter attributes table by name \'Color\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterAttributes', baseContext);

    await boAttributesPage.filterTable(page, 'name', dataAttributes.color.name);

    const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
    expect(textColumn).to.contains(dataAttributes.color.name);
  });

  it('should view attribute', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewAttribute', baseContext);

    await boAttributesPage.viewAttribute(page, 1);

    const pageTitle = await boAttributesViewPage.getPageTitle(page);
    expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(dataAttributes.color.name));
  });

  it('should reset all filters and get number of values in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfValues = await boAttributesViewPage.resetAndGetNumberOfLines(page);
    expect(numberOfValues).to.be.above(0);
  });

  describe('Filter values table', async () => {
    [
      {
        testIdentifier: 'filterId',
        filterBy: 'id_attribute',
        filterValue: dataAttributes.color.values[13].id.toString(),
      },
      {
        testIdentifier: 'filterName',
        filterBy: 'name',
        filterValue: dataAttributes.color.values[3].value,
      },
      {
        testIdentifier: 'filterColor',
        filterBy: 'color',
        filterValue: dataAttributes.color.values[7].color,
      },
    ].forEach((test: {testIdentifier: string, filterBy: string, filterValue: string}) => {
      it(`should filter by ${test.filterBy} '${test.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        await boAttributesViewPage.filterTable(
          page,
          test.filterBy,
          test.filterValue,
        );

        const numberOfValuesAfterFilter = await boAttributesViewPage.getNumberOfElementInGrid(page);
        expect(numberOfValuesAfterFilter).to.be.at.most(numberOfValues);

        for (let nthRow: number = 1; nthRow <= numberOfValuesAfterFilter; nthRow++) {
          const textColumn = await boAttributesViewPage.getTextColumn(page, nthRow, test.filterBy);
          expect(textColumn).to.contains(test.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}Reset`, baseContext);

        const numberOfValuesAfterReset = await boAttributesViewPage.resetAndGetNumberOfLines(page);
        expect(numberOfValuesAfterReset).to.equal(numberOfValues);
      });
    });
  });
});
