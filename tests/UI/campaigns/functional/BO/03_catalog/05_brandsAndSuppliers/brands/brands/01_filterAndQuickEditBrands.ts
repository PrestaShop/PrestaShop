// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boBrandsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataBrands,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_brands_brands_filterAndQuickEditBrands';

// Filter and quick edit brands
describe('BO - Catalog - Brands & suppliers : Filter and quick edit Brands table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfBrands: number = 0;

  const tableName: string = 'manufacturer';

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

  // Go to Brands Page
  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.brandsAndSuppliersLink,
    );
    await boBrandsPage.closeSfToolBar(page);

    const pageTitle = await boBrandsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boBrandsPage.pageTitle);
  });

  it('should reset all filters and get number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfBrands = await boBrandsPage.resetAndGetNumberOfLines(page, tableName);
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
            filterValue: dataBrands.brand_1.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: dataBrands.brand_1.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: dataBrands.brand_1.enabled ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        if (test.args.filterBy === 'active') {
          await boBrandsPage.filterBrandsEnabled(page, test.args.filterValue);
        } else {
          await boBrandsPage.filterBrands(
            page,
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }

        const numberOfBrandsAfterFilter = await boBrandsPage.getNumberOfElementInGrid(page, tableName);
        expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

        for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const brandStatus = await boBrandsPage.getBrandStatus(page, i);
            expect(brandStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boBrandsPage.getTextColumnFromTableBrands(page, i, test.args.filterBy);
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfBrandsAfterReset = await boBrandsPage.resetAndGetNumberOfLines(page, tableName);
        expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
      });
    });
  });

  // 2 : Quick edit brands in list
  describe('Quick edit Brands', async () => {
    it('should filter by brand name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await boBrandsPage.filterBrands(page, 'input', 'name', dataBrands.brand_1.name);

      const numberOfBrandsAfterFilter = await boBrandsPage.getNumberOfElementInGrid(page, tableName);
      expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      const textColumn = await boBrandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      expect(textColumn).to.contains(dataBrands.brand_1.name);
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} first brand`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Brand`, baseContext);

        const isActionPerformed = await boBrandsPage.setBrandStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await boBrandsPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(boBrandsPage.successfulUpdateStatusMessage);
        }

        const brandsStatus = await boBrandsPage.getBrandStatus(page, 1);
        expect(brandsStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfBrandsAfterReset = await boBrandsPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });
  });
});
