import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boDashboardPage,
  boFeaturesPage,
  boLoginPage,
  type BrowserContext,
  dataFeatures,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_features_filterFeatures';

// Filter features table by id, name and position
describe('BO - Catalog - Attributes & Features : Filter features table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfFeatures: number = 0;

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

  it('should go to \'Catalog > Attributes & features\' page', async function () {
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

  it('should go to features page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await boAttributesPage.goToFeaturesPage(page);

    const pageTitle = await boFeaturesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boFeaturesPage.pageTitle);
  });

  it('should reset all filters and get number of features in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfFeatures = await boFeaturesPage.resetAndGetNumberOfLines(page);
    expect(numberOfFeatures).to.be.above(0);
  });

  describe('Filter features', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterBy: 'id_feature', filterValue: dataFeatures.composition.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterBy: 'name', filterValue: dataFeatures.composition.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterPosition',
          filterBy: 'position',
          filterValue: dataFeatures.composition.position,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boFeaturesPage.filterTable(
          page,
          test.args.filterBy,
          typeof test.args.filterValue === 'number' ? test.args.filterValue.toString() : test.args.filterValue,
        );

        const numberOfFeaturesAfterFilter = await boFeaturesPage.getNumberOfElementInGrid(page);
        expect(numberOfFeaturesAfterFilter).to.be.at.most(numberOfFeatures);

        const textColumn = await boFeaturesPage.getTextColumn(
          page,
          1,
          test.args.filterBy,
        );
        expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfFeaturesAfterReset = await boFeaturesPage.resetAndGetNumberOfLines(page);
        expect(numberOfFeaturesAfterReset).to.equal(numberOfFeatures);
      });
    });
  });
});
