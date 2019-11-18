require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const BrandFaker = require('@data/faker/brand');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');
const AddBrandPage = require('@pages/BO/catalog/brands/add');

let browser;
let page;
let numberOfBrands = 0;
const firstBrandData = new BrandFaker({name: 'BrandToDelete'});
const secondBrandData = new BrandFaker({name: 'BrandToDelete2'});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    addBrandPage: new AddBrandPage(page),
  };
};

// Create 2 brands, Enable, disable and delete with bulk actions
describe('Create 2 brands, Enable, disable and delete with bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await Promise.all([
      files.deleteFile(firstBrandData.logo),
      files.deleteFile(secondBrandData.logo),
    ]);
  });
  // Login into BO and go to brands page
  loginCommon.loginBO();

  // GO to Brands Page
  it('should go to brands page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.brandsAndSuppliersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all Brands filters', async function () {
    numberOfBrands = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });
  // 1: Create 2 Brands
  describe('Create 2 Brands', async () => {
    const brandsToCreate = [firstBrandData, secondBrandData];
    brandsToCreate.forEach((brandToCreate, index) => {
      it('should go to new brand page', async function () {
        await this.pageObjects.brandsPage.goToAddNewBrandPage();
        const pageTitle = await this.pageObjects.addBrandPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addBrandPage.pageTitle);
      });

      it('should create new brand', async function () {
        const result = await this.pageObjects.addBrandPage.createEditBrand(brandToCreate);
        await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);
        const numberOfBrandsAfterCreation = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
        await expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + index + 1);
      });
    });
  });
  // 2 : Disable, enable Brands
  describe('Disable, enable created Brands', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', 'BrandToDelete');
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableBrands(i, 'name');
        await expect(textColumn).to.contains('BrandToDelete');
      }
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}, expected: 'clear'},
      {args: {action: 'enable', enabledValue: true}, expected: 'check'},
    ];
    tests.forEach((test) => {
      it(`should ${test.args.action} brands`, async function () {
        const textResult = await this.pageObjects.brandsPage.changeBrandsEnabledColumnBulkActions(
          test.args.enabledValue,
        );
        await expect(textResult).to.be.equal(this.pageObjects.brandsPage.successfulUpdateStatusMessage);
        const numberOfBrandsInGrid = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
        await expect(numberOfBrandsInGrid).to.be.at.most(numberOfBrands);
        for (let i = 1; i <= numberOfBrandsInGrid; i++) {
          const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableBrands(i, 'active');
          await expect(textColumn).to.contains(test.expected);
        }
      });
    });

    it('should reset Brand filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 2);
    });
  });
  // 3 : Delete Brands created with bulk actions
  describe('Delete Brands with Bulk Actions', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', 'BrandToDelete');
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableBrands(i, 'name');
        await expect(textColumn).to.contains('BrandToDelete');
      }
    });

    it('should delete Brands with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.brandsPage.deleteWithBulkActions('manufacturer');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });

    it('should reset Brand filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands);
    });
  });
});
