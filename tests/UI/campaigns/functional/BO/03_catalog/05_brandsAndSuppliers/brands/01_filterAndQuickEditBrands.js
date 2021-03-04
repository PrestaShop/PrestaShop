require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const {demoBrands} = require('@data/demo/brands');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_filterQuickEditBrands';

let browserContext;
let page;
let numberOfBrands = 0;

// Filter And Quick Edit brands
describe('Filter And Quick Edit brands', async () => {
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

  // GO to Brands Page
  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.brandsAndSuppliersLink,
    );

    await brandsPage.closeSfToolBar(page);

    const pageTitle = await brandsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  it('should reset all filters and get Number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  // 1 : Filter brands
  describe('Filter brands', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_manufacturer',
            filterValue: demoBrands.first.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: demoBrands.first.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: demoBrands.first.enabled,
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

        const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');
        await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

        for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const brandStatus = await brandsPage.getBrandStatus(page, i);
            await expect(brandStatus).to.equal(test.args.filterValue);
          } else {
            const textColumn = await brandsPage.getTextColumnFromTableBrands(page, i, test.args.filterBy);
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
        await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
      });
    });
  });

  // 2 : Edit brands in list
  describe('Quick Edit brands', async () => {
    it('should filter by brand name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', demoBrands.first.name);
      const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      await expect(textColumn).to.contains(demoBrands.first.name);
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first brand`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Brand`, baseContext);

        const isActionPerformed = await brandsPage.setBrandStatus(page, 1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await brandsPage.getAlertSuccessBlockParagraphContent(page);
          await expect(resultMessage).to.contains(brandsPage.successfulUpdateStatusMessage);
        }

        const brandsStatus = await brandsPage.getBrandStatus(page, 1);
        await expect(brandsStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
      await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });
  });
});
