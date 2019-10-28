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
const BrandsPage = require('@pages/BO/brands');
const AddBrandPage = require('@pages/BO/addBrand');

let browser;
let page;
let numberOfBrands = 0;
let firstBrandData;
let secondBrandData;

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
    firstBrandData = await (new BrandFaker({name: 'BrandToDelete'}));
    secondBrandData = await (new BrandFaker({name: 'BrandToDelete2'}));
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
      this.pageObjects.boBasePage.productsParentLink,
      this.pageObjects.boBasePage.brandsAndSuppliersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all Brands filters', async function () {
    numberOfBrands = await this.pageObjects.brandsPage.resetFilters('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });
  // 1: Create 2 Brands
  describe('Create 2 Brands', async () => {
    it('should go to new brand page', async function () {
      await this.pageObjects.brandsPage.goToAddNewBrandPage();
      const pageTitle = await this.pageObjects.addBrandPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addBrandPage.pageTitle);
    });

    it('should create first brand', async function () {
      const result = await this.pageObjects.addBrandPage.createEditBrand(firstBrandData);
      await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);
      const numberOfBrandsAfterCreation = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + 1);
    });

    it('should go to new brand page', async function () {
      await this.pageObjects.brandsPage.goToAddNewBrandPage();
      const pageTitle = await this.pageObjects.addBrandPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addBrandPage.pageTitle);
    });

    it('should create second brand', async function () {
      const result = await this.pageObjects.addBrandPage.createEditBrand(secondBrandData);
      await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);
      const numberOfBrandsAfterCreation = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + 2);
    });
  });
  // 2 : Disable, enable Brands
  describe('Disable, enable created Brands', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', 'BrandToDelete');
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextContent(
          this.pageObjects.brandsPage.tableColumn
            .replace('%TABLE', 'manufacturer')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'name'),
        );
        await expect(textColumn).to.contains('BrandToDelete');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should disable brands', async function () {
      const disableTextResult = await this.pageObjects.brandsPage.changeBrandsEnabledColumnBulkActions(false);
      await expect(disableTextResult).to.be.equal(this.pageObjects.brandsPage.successfulUpdateStatusMessage);
      const numberOfBrandsInGrid = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsInGrid).to.be.at.most(numberOfBrands);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfBrandsInGrid; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextContent(
          this.pageObjects.brandsPage.tableColumn
            .replace('%TABLE', 'manufacturer')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('clear');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should enable brands', async function () {
      const disableTextResult = await this.pageObjects.brandsPage.changeBrandsEnabledColumnBulkActions(true);
      await expect(disableTextResult).to.be.equal(this.pageObjects.brandsPage.successfulUpdateStatusMessage);
      const numberOfBrandsInGrid = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsInGrid).to.be.at.most(numberOfBrands);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfBrandsInGrid; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextContent(
          this.pageObjects.brandsPage.tableColumn
            .replace('%TABLE', 'manufacturer')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'active'),
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset Brand filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 2);
    });
  });
  // 3 : Delete Brands created with bulk actions
  describe('Delete Brands with Bulk Actions', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', 'BrandToDelete');
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextContent(
          this.pageObjects.brandsPage.tableColumn
            .replace('%TABLE', 'manufacturer')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'name'),
        );
        await expect(textColumn).to.contains('BrandToDelete');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should delete Brands with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.brandsPage.deleteWithBulkActions('manufacturer');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });

    it('should reset Brand filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands);
    });
  });
});
