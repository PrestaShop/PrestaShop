require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const BrandFaker = require('@data/faker/brand');
const BrandAddressFaker = require('@data/faker/brandAddress');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/brands');
const AddBrandPage = require('@pages/BO/addBrand');
const ViewBrandPage = require('@pages/BO/viewBrand');
const AddBrandAddressPage = require('@pages/BO/addBrandAddress');

let browser;
let page;
let numberOfBrands = 0;
let numberOfBrandsAddresses = 0;
let createBrandData;
let editBrandData;
let createBrandAddressData;
let editBrandAddressData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    addBrandPage: new AddBrandPage(page),
    viewBrandPage: new ViewBrandPage(page),
    addBrandAddressPage: new AddBrandAddressPage(page),
  };
};

// CRUD Brand And Address
describe('Create, Update and Delete Brand and Address', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createBrandData = await (new BrandFaker());
    editBrandData = await (new BrandFaker());
    createBrandAddressData = await (new BrandAddressFaker({brandName: createBrandData.name}));
    editBrandAddressData = await (new BrandAddressFaker({brandName: editBrandData.name}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await Promise.all([
      files.deleteFile(createBrandData.logo),
      files.deleteFile(editBrandData.logo),
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

  it('should reset all filters', async function () {
    numberOfBrands = await this.pageObjects.brandsPage.resetFilters('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
    numberOfBrandsAddresses = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
    await expect(numberOfBrandsAddresses).to.be.above(0);
  });
  // 1: Create Brand
  describe('Create Brand', async () => {
    it('should go to new brand page', async function () {
      await this.pageObjects.brandsPage.goToAddNewBrandPage();
      const pageTitle = await this.pageObjects.addBrandPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addBrandPage.pageTitle);
    });

    it('should create brand', async function () {
      const result = await this.pageObjects.addBrandPage.createEditBrand(createBrandData);
      await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);
      const numberOfBrandsAfterCreation = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + 1);
    });
  });
  // 2: Create Address for this Brand
  describe('Create Address associated to created Brand', async () => {
    it('should go to new brand address page', async function () {
      await this.pageObjects.brandsPage.goToAddNewBrandAddressPage();
      const pageTitle = await this.pageObjects.addBrandAddressPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addBrandAddressPage.pageTitle);
    });

    it('should create brand address', async function () {
      const result = await this.pageObjects.addBrandAddressPage.createEditBrandAddress(createBrandAddressData);
      await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);
      const numberOfBrandsAddressesAfterCreation = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer_address'),
      );
      createBrandData.addresses += 1;
      await expect(numberOfBrandsAddressesAfterCreation).to.be.equal(numberOfBrandsAddresses + 1);
    });
  });
  // 3 : View Brand and check Address Value in list
  describe('View Brand and check Address Value in list', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', createBrandData.name);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(createBrandData.name);
    });

    it('should view brand', async function () {
      await this.pageObjects.brandsPage.viewBrand('1');
      const pageTitle = await this.pageObjects.viewBrandPage.getPageTitle();
      await expect(pageTitle).to.contains(createBrandData.name);
    });

    it('should check existence of the associated address', async function () {
      const numberOfAddressesInGrid = await this.pageObjects.viewBrandPage.getNumberFromText(
        this.pageObjects.viewBrandPage.addressesGridHeader,
      );
      await expect(numberOfAddressesInGrid).to.equal(createBrandData.addresses);
      const textColumn = await this.pageObjects.viewBrandPage.getTextContent(
        this.pageObjects.viewBrandPage.addressesTableColumn.replace('%ROW', '1').replace('%COLUMN', '1'),
      );
      await expect(textColumn).to.contains(`${createBrandAddressData.firstName} ${createBrandAddressData.lastName}`);
    });

    it('should return brands Page', async function () {
      await this.pageObjects.viewBrandPage.goToPreviousPage();
      const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
    });

    it('should reset brands filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 1);
    });
  });
  // 4: Update Brand And Verify Brand in Address List
  describe('Update Brand And Verify Brand in Address List', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', createBrandData.name);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(createBrandData.name);
    });

    it('should go to edit brand page', async function () {
      await this.pageObjects.brandsPage.goToEditBrandPage('1');
      const pageTitle = await this.pageObjects.addBrandPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addBrandPage.pageTitleEdit);
    });

    it('should edit brand', async function () {
      const result = await this.pageObjects.addBrandPage.createEditBrand(editBrandData);
      editBrandData.addresses += 1;
      await expect(result).to.equal(this.pageObjects.brandsPage.successfulUpdateMessage);
    });

    it('should check the update in Brand Address List', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'name', editBrandData.name);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(editBrandData.name);
    });

    it('should reset all filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 1);

      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.be.equal(numberOfBrandsAddresses + 1);
    });
  });
  // 5: Update Address
  describe('Update Address', async () => {
    it('should filter Brand Address list by name of edited brand', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'name', editBrandData.name);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(editBrandData.name);
    });

    it('should go to edit brand address page', async function () {
      await this.pageObjects.brandsPage.goToEditBrandAddressPage('1');
      const pageTitle = await this.pageObjects.addBrandPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
    });

    it('should edit brand address', async function () {
      const result = await this.pageObjects.addBrandAddressPage.createEditBrandAddress(editBrandAddressData);
      await expect(result).to.equal(this.pageObjects.brandsPage.successfulUpdateMessage);
    });

    it('should reset Brand Addresses filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.be.equal(numberOfBrandsAddresses + 1);
    });
  });
  // 6 : View Brand and check Address Value in list
  describe('View Brand and check Address Value in list', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', editBrandData.name);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(editBrandData.name);
    });

    it('should view brand', async function () {
      await this.pageObjects.brandsPage.viewBrand('1');
      const pageTitle = await this.pageObjects.viewBrandPage.getPageTitle();
      await expect(pageTitle).to.contains(editBrandData.name);
    });

    it('should check existence of the associated address', async function () {
      const numberOfAddressesInGrid = await this.pageObjects.viewBrandPage.getNumberFromText(
        this.pageObjects.viewBrandPage.addressesGridHeader,
      );
      await expect(numberOfAddressesInGrid).to.equal(editBrandData.addresses);
      const textColumn = await this.pageObjects.viewBrandPage.getTextContent(
        this.pageObjects.viewBrandPage.addressesTableColumn.replace('%ROW', '1').replace('%COLUMN', '1'),
      );
      await expect(textColumn).to.contains(`${editBrandAddressData.firstName} ${editBrandAddressData.lastName}`);
    });

    it('should return brands Page', async function () {
      await this.pageObjects.viewBrandPage.goToPreviousPage();
      const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
    });

    it('should reset brands filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 1);
    });
  });
  // 7 : Delete Brand And Verify that Address has no Brand associated
  describe('Delete Brand And Verify that Address has no Brand associated', async () => {
    it('should filter Brand list by name of edited brand', async function () {
      await this.pageObjects.brandsPage.filterBrands('input', 'name', editBrandData.name);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberFromText(
        this.pageObjects.brandsPage.gridHeaderTitle.replace('%TABLE', 'manufacturer'),
      );
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(editBrandData.name);
    });

    it('should Delete brand', async function () {
      const result = await this.pageObjects.brandsPage.deleteBrand('1');
      await expect(result).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });

    it('should check the delete in Brand Address List', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'firstname', editBrandAddressData.firstName);
      await this.pageObjects.brandsPage.filterAddresses('input', 'lastname', editBrandAddressData.lastName);
      const textColumn = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains('--');
    });

    it('should reset Brand filters', async function () {
      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands);
    });
  });
  // 8 : Delete Address
  describe('Delete brand Address', async () => {
    it('should filter Brand Address list by firstName and lastName', async function () {
      await this.pageObjects.brandsPage.filterAddresses('input', 'firstname', editBrandAddressData.firstName);
      await this.pageObjects.brandsPage.filterAddresses('input', 'lastname', editBrandAddressData.lastName);
      const textColumnFirstName = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'firstname'),
      );
      await expect(textColumnFirstName).to.contains(editBrandAddressData.firstName);
      const textColumnLastName = await this.pageObjects.brandsPage.getTextContent(
        this.pageObjects.brandsPage.tableColumn
          .replace('%TABLE', 'manufacturer_address')
          .replace('%ROW', 1)
          .replace('%COLUMN', 'lastname'),
      );
      await expect(textColumnLastName).to.contains(editBrandAddressData.lastName);
    });

    it('should Delete Brand Address', async function () {
      const result = await this.pageObjects.brandsPage.deleteBrandAddress('1');
      await expect(result).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });

    it('should reset Brand Addresses filters', async function () {
      const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetFilters('manufacturer_address');
      await expect(numberOfBrandsAddressesAfterReset).to.be.equal(numberOfBrandsAddresses);
    });
  });
});
