require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const brandsCommon = require('@commonTests/BO/brands');
const {demoAddresses} = require('@data/demo/brands');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/brands');

let browser;
let page;
let numberOfBrandsAddresses = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
  };
};

// Filter And Quick Edit Addresses
describe('Filter And Quick Edit Addresses', async () => {
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
  brandsCommon.goToBrandsPage();

  it('should reset all filters and get Number of brands in BO', async function () {
    numberOfBrandsAddresses = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
    await expect(numberOfBrandsAddresses).to.be.above(0);
  });
  // 1 : Filter brands
  describe('Filter brands addresses', async () => {
    it('should filter by Id', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'id_address', demoAddresses.first.id);
      const numberOfBrandsAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandsAddressesAfterFilter).to.be.at.most(numberOfBrandsAddresses);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn.replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1).replace('%COLUMN', 'id_address'),
      );
      await expect(textColumn).to.contains(demoAddresses.first.id);
    });
    it('should reset all filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.equal(numberOfBrandsAddresses);
    });
    it('should filter by brand name', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'name', demoAddresses.first.brand);
      const numberOfBrandsAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandsAddressesAfterFilter).to.be.at.most(numberOfBrandsAddresses);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(demoAddresses.first.brand);
    });
    it('should reset all filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.equal(numberOfBrandsAddresses);
    });
    it('should filter by Manufacturer firstname', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'firstname', demoAddresses.first.firstName);
      const numberOfBrandsAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandsAddressesAfterFilter).to.be.at.most(numberOfBrandsAddresses);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'firstname'),
      );
      await expect(textColumn).to.contains(demoAddresses.first.firstName);
    });
    it('should reset all filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.equal(numberOfBrandsAddresses);
    });
    it('should filter by Manufacturer lastname', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'lastname', demoAddresses.first.lastName);
      const numberOfBrandsAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandsAddressesAfterFilter).to.be.at.most(numberOfBrandsAddresses);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'lastname'),
      );
      await expect(textColumn).to.contains(demoAddresses.first.lastName);
    });
    it('should reset all filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.equal(numberOfBrandsAddresses);
    });
    it('should filter by Address postal code', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'postcode', demoAddresses.first.postalCode);
      const numberOfBrandsAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandsAddressesAfterFilter).to.be.at.most(numberOfBrandsAddresses);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'postcode'),
      );
      await expect(textColumn).to.contains(demoAddresses.first.postalCode);
    });
    it('should reset all filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.equal(numberOfBrandsAddresses);
    });
    it('should filter by City', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'city', demoAddresses.first.city);
      const numberOfBrandsAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandsAddressesAfterFilter).to.be.at.most(numberOfBrandsAddresses);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'city'),
      );
      await expect(textColumn).to.contains(demoAddresses.first.city);
    });
    it('should reset all filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.equal(numberOfBrandsAddresses);
    });
    it('should filter by Country', async function () {
      await this.pageObjects.brandsPage.filterAddresses('select', 'country', demoAddresses.first.country);
      const numberOfBrandsAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      await expect(numberOfBrandsAddressesAfterFilter).to.be.at.most(numberOfBrandsAddresses);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'country'),
      );
      await expect(textColumn).to.contains(demoAddresses.first.country);
    });
    it('should reset all filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.equal(numberOfBrandsAddresses);
    });
  });
});
