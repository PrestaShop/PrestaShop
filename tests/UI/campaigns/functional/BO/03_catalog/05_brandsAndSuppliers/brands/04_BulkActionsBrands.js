require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import data
const BrandFaker = require('@data/faker/brand');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const addBrandPage = require('@pages/BO/catalog/brands/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_bulkActionsBrands';

let browserContext;
let page;
let numberOfBrands = 0;

const firstBrandData = new BrandFaker({name: 'BrandToDelete'});
const secondBrandData = new BrandFaker({name: 'BrandToDelete2'});

// Create 2 brands, Enable, disable and delete with bulk actions
describe('Create 2 brands, Enable, disable and delete with bulk actions', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create logos
    await Promise.all([
      files.generateImage(firstBrandData.logo),
      files.generateImage(secondBrandData.logo),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      files.deleteFile(firstBrandData.logo),
      files.deleteFile(secondBrandData.logo),
    ]);
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

  it('should reset all Brands filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  // 1: Create 2 Brands
  describe('Create 2 Brands', async () => {
    const brandsToCreate = [firstBrandData, secondBrandData];

    brandsToCreate.forEach((brandToCreate, index) => {
      it('should go to new brand page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddBrandPage${index + 1}`, baseContext);

        await brandsPage.goToAddNewBrandPage(page);
        const pageTitle = await addBrandPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addBrandPage.pageTitle);
      });

      it('should create new brand', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createBrand${index + 1}`, baseContext);

        const result = await addBrandPage.createEditBrand(page, brandToCreate);
        await expect(result).to.equal(brandsPage.successfulCreationMessage);

        const numberOfBrandsAfterCreation = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');
        await expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + index + 1);
      });
    });
  });

  // 2 : Disable, enable Brands
  describe('Disable, enable created Brands', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEdit', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', 'BrandToDelete');

      const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
        const textColumn = await brandsPage.getTextColumnFromTableBrands(page, i, 'name');
        await expect(textColumn).to.contains('BrandToDelete');
      }
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} brands`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Brand`, baseContext);

        const textResult = await brandsPage.bulkSetBrandsStatus(
          page,
          test.args.enabledValue,
        );

        await expect(textResult).to.be.equal(brandsPage.successfulUpdateStatusMessage);

        const numberOfBrandsInGrid = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');
        await expect(numberOfBrandsInGrid).to.be.at.most(numberOfBrands);

        for (let i = 1; i <= numberOfBrandsInGrid; i++) {
          const brandStatus = await brandsPage.getBrandStatus(page, i);
          await expect(brandStatus).to.equal(test.args.enabledValue);
        }
      });
    });

    it('should reset Brand filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkEdit', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 2);
    });
  });

  // 3 : Delete Brands created with bulk actions
  describe('Delete Brands with Bulk Actions', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', 'BrandToDelete');

      const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
        const textColumn = await brandsPage.getTextColumnFromTableBrands(page, i, 'name');
        await expect(textColumn).to.contains('BrandToDelete');
      }
    });

    it('should delete Brands with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteBrands', baseContext);

      const deleteTextResult = await brandsPage.deleteWithBulkActions(page, 'manufacturer');
      await expect(deleteTextResult).to.be.equal(brandsPage.successfulDeleteMessage);
    });

    it('should reset Brand filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands);
    });
  });
});
