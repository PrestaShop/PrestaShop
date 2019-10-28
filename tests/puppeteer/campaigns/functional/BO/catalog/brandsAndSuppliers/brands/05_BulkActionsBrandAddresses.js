require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const BrandAddressFaker = require('@data/faker/brandAddress');
const loginCommon = require('@commonTests/loginBO');
const brandsCommon = require('@commonTests/BO/BOBase');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/brands');
const AddBrandAddressPage = require('@pages/BO/addBrandAddress');

let browser;
let page;
let numberOfBrandAddresses = 0;
let firstAddressData;
let secondAddressData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    addBrandAddressPage: new AddBrandAddressPage(page),
  };
};

// Create 2 brands, Enable, disable and delete with bulk actions
describe('Create 2 brand Addresses and delete with bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    firstAddressData = await (new BrandAddressFaker({firstName: 'AddressToDelete'}));
    secondAddressData = await (new BrandAddressFaker({firstName: 'AddressToDeleteTwo'}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to brands page
  loginCommon.loginBO();

  // GO to Brands Page
  brandsCommon.goToBrandsPage();
  
  it('should reset all Addresses filters', async function () {
    await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
    numberOfBrandAddresses = await this.pageObjects.brandsPage.getNumberFromText(
      this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
    );
    await expect(numberOfBrandAddresses).to.be.above(0);
  });
  // 1: Create 2 Addresses
  describe('Create 2 Addresses', async () => {
    it('should go to new brand Address page', async function () {
      await this.pageObjects.brandsPage.goToAddNewBrandAddressPage();
      const pageTitle = await this.pageObjects.addBrandAddressPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addBrandAddressPage.pageTitle);
    });
    it('should create first brand', async function () {
      const result = await this.pageObjects.addBrandAddressPage.createEditBrandAddress(firstAddressData);
      await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);
      const numberOfBrandAddressesAfterCreation = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandAddressesAfterCreation).to.be.equal(numberOfBrandAddresses + 1);
    });
    it('should go to new brand Address page', async function () {
      await this.pageObjects.brandsPage.goToAddNewBrandAddressPage();
      const pageTitle = await this.pageObjects.addBrandAddressPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addBrandAddressPage.pageTitle);
    });
    it('should create second brand', async function () {
      const result = await this.pageObjects.addBrandAddressPage.createEditBrandAddress(secondAddressData);
      await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);
      const numberOfBrandAddressesAfterCreation = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandAddressesAfterCreation).to.be.equal(numberOfBrandAddresses + 2);
    });
  });
  // 2 : Delete Brand Addresses created with bulk actions
  describe('Delete Addresses with Bulk Actions', async () => {
    it('should filter Addresses list by firstName', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'firstname', 'AddressToDelete');
      const numberOfBrandAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandAddressesAfterFilter).to.be.at.most(numberOfBrandAddresses);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfBrandAddressesAfterFilter; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextContent(
          this.pageObjects.brandsPage.tableColumn
            .replace('%TABLE', 'manufacturer_address')
            .replace('%ROW', 1)
            .replace('%COLUMN', 'firstname'),
        );
        await expect(textColumn).to.contains('AddressToDelete');
      }
      /* eslint-enable no-await-in-loop */
    });
    it('should delete Addresses with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.brandsPage.deleteWithBulkActions('manufacturer_address');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });
    it('should reset Addresses filters', async function () {
      await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      const numberOfBrandAddressesAfterReset = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandAddressesAfterReset).to.be.equal(numberOfBrandAddresses);
    });
  });
});
