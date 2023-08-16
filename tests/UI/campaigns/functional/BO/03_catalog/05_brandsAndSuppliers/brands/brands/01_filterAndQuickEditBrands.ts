// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import brandsPage from '@pages/BO/catalog/brands';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Brands from '@data/demo/brands';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_brands_brands_filterAndQuickEditBrands';

// Filter and quick edit brands
describe('BO - Catalog - Brands & suppliers : Filter and quick edit Brands table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfBrands: number = 0;

  const tableName: string = 'manufacturer';

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

  // Go to Brands Page
  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.brandsAndSuppliersLink,
    );
    await brandsPage.closeSfToolBar(page);

    const pageTitle = await brandsPage.getPageTitle(page);
    expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  it('should reset all filters and get number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfBrands).to.be.above(0);
  });

  // 1 : Filter brands table
  describe('Filter Brands table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_manufacturer',
            filterValue: Brands.first.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Brands.first.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Brands.first.enabled ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        if (test.args.filterBy === 'active') {
          await brandsPage.filterBrandsEnabled(page, test.args.filterValue);
        } else {
          await brandsPage.filterBrands(
            page,
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }

        const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, tableName);
        expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

        for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const brandStatus = await brandsPage.getBrandStatus(page, i);
            expect(brandStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await brandsPage.getTextColumnFromTableBrands(page, i, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, tableName);
        expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
      });
    });
  });

  // 2 : Quick edit brands in list
  describe('Quick edit Brands', async () => {
    it('should filter by brand name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', Brands.first.name);

      const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, tableName);
      expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      expect(textColumn).to.contains(Brands.first.name);
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} first brand`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Brand`, baseContext);

        const isActionPerformed = await brandsPage.setBrandStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await brandsPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(brandsPage.successfulUpdateStatusMessage);
        }

        const brandsStatus = await brandsPage.getBrandStatus(page, 1);
        expect(brandsStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });
  });
});
