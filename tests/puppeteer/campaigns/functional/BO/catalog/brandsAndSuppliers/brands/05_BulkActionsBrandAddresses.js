require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const BrandAddressFaker = require('@data/faker/brandAddress');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');
const AddBrandAddressPage = require('@pages/BO/catalog/brands/addresses/add');

let browser;
let page;
let numberOfBrandAddresses = 0;
const firstAddressData = new BrandAddressFaker({firstName: 'AddressToDelete'});
const secondAddressData = new BrandAddressFaker({firstName: 'AddressToDeleteTwo'});

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
  });
  after(async () => {
    await helper.closeBrowser(browser);
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

  it('should reset all Addresses filters', async function () {
    numberOfBrandAddresses = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer_address');
    await expect(numberOfBrandAddresses).to.be.above(0);
  });
  // 1: Create 2 Addresses
  describe('Create 2 Addresses', async () => {
    const addressesToCreate = [firstAddressData, secondAddressData];
    addressesToCreate.forEach((addressToCreate, index) => {
      it('should go to new brand Address page', async function () {
        await this.pageObjects.brandsPage.goToAddNewBrandAddressPage();
        const pageTitle = await this.pageObjects.addBrandAddressPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addBrandAddressPage.pageTitle);
      });

      it('should create new brand Address', async function () {
        const result = await this.pageObjects.addBrandAddressPage.createEditBrandAddress(addressToCreate);
        await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);
        const numberOfBrandAddressesAfterCreation = await this.pageObjects.brandsPage.getNumberOfElementInGrid(
          'manufacturer_address',
        );
        await expect(numberOfBrandAddressesAfterCreation).to.be.equal(numberOfBrandAddresses + index + 1);
      });
    });
  });
  // 2 : Delete Brand Addresses created with bulk actions
  describe('Delete Addresses with Bulk Actions', async () => {
    it('should filter Addresses list by firstName', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'firstname', 'AddressToDelete');
      const numberOfBrandAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid(
        'manufacturer_address',
      );
      await expect(numberOfBrandAddressesAfterFilter).to.be.at.most(numberOfBrandAddresses);
      for (let i = 1; i <= numberOfBrandAddressesAfterFilter; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableAddresses(i, 'firstname');
        await expect(textColumn).to.contains('AddressToDelete');
      }
    });

    it('should delete Addresses with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.brandsPage.deleteWithBulkActions('manufacturer_address');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });

    it('should reset Addresses filters', async function () {
      const numberOfBrandAddressesAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines(
        'manufacturer_address',
      );
      await expect(numberOfBrandAddressesAfterReset).to.be.equal(numberOfBrandAddresses);
    });
  });
});
