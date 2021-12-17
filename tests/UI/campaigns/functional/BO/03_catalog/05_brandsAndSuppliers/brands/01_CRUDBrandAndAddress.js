require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const BrandFaker = require('@data/faker/brand');
const BrandAddressFaker = require('@data/faker/brandAddress');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const addBrandPage = require('@pages/BO/catalog/brands/add');
const viewBrandPage = require('@pages/BO/catalog/brands/view');
const addBrandAddressPage = require('@pages/BO/catalog/brands/addAddress');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_CRUDBrandAndAddress';

let browserContext;
let page;

let numberOfBrands = 0;
let numberOfBrandsAddresses = 0;
const brandsTable = 'manufacturer';
const addressesTable = 'manufacturer_address';

const createBrandData = new BrandFaker();
const editBrandData = new BrandFaker();

const createBrandAddressData = new BrandAddressFaker({brandName: createBrandData.name});
const editBrandAddressData = new BrandAddressFaker({brandName: editBrandData.name});

// CRUD Brand And Address
describe('BO - Catalog - Brands & suppliers : CRUD Brand and Address', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create logos
    await Promise.all([
      files.generateImage(createBrandData.logo),
      files.generateImage(editBrandData.logo),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      files.deleteFile(createBrandData.logo),
      files.deleteFile(editBrandData.logo),
    ]);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // GO to Brands Page
  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
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

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

    numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, brandsTable);
    await expect(numberOfBrands).to.be.above(0);

    numberOfBrandsAddresses = await brandsPage.resetAndGetNumberOfLines(page, addressesTable);
    await expect(numberOfBrandsAddresses).to.be.above(0);
  });

  // 1: Create Brand
  describe('Create Brand', async () => {
    it('should go to new brand page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddBrandPage', baseContext);

      await brandsPage.goToAddNewBrandPage(page);

      const pageTitle = await addBrandPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addBrandPage.pageTitle);
    });

    it('should create brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createBrand', baseContext);

      const result = await addBrandPage.createEditBrand(page, createBrandData);
      await expect(result).to.equal(brandsPage.successfulCreationMessage);

      const numberOfBrandsAfterCreation = await brandsPage.getNumberOfElementInGrid(page, brandsTable);
      await expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + 1);
    });
  });

  // 2: Create Address for this Brand
  describe('Create Address associated to created Brand', async () => {
    it('should go to new brand address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddAddressPage', baseContext);

      await brandsPage.goToAddNewBrandAddressPage(page);

      const pageTitle = await addBrandAddressPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addBrandAddressPage.pageTitle);
    });

    it('should create brand address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const result = await addBrandAddressPage.createEditBrandAddress(page, createBrandAddressData);
      await expect(result).to.equal(brandsPage.successfulCreationMessage);

      const numberOfBrandsAddressesAfterCreation = await brandsPage.getNumberOfElementInGrid(page, addressesTable);

      createBrandData.addresses += 1;
      await expect(numberOfBrandsAddressesAfterCreation).to.be.equal(numberOfBrandsAddresses + 1);
    });
  });

  // 3 : View Brand and check Address Value in list
  describe('View Brand and check Address Value in list', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedBrand', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', createBrandData.name);

      const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, brandsTable);
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      await expect(textColumn).to.contains(createBrandData.name);
    });

    it('should view brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedBrand', baseContext);

      await brandsPage.viewBrand(page, 1);

      const pageTitle = await viewBrandPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createBrandData.name);
    });

    it('should check existence of the associated address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressOnCreatedBrand', baseContext);

      const numberOfAddressesInGrid = await viewBrandPage.getNumberFromText(
        page,
        viewBrandPage.addressesGridHeader,
      );

      await expect(numberOfAddressesInGrid).to.equal(createBrandData.addresses);

      const textColumn = await viewBrandPage.getTextColumnFromTableAddresses(page, 1, 1);
      await expect(textColumn).to.contains(`${createBrandAddressData.firstName} ${createBrandAddressData.lastName}`);
    });

    it('should return brands Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPageAfterViewCreatedBrand', baseContext);

      await viewBrandPage.goToPreviousPage(page);

      const pageTitle = await brandsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(brandsPage.pageTitle);
    });

    it('should reset brands filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterViewCreatedBrand', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, brandsTable);
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 1);
    });
  });

  // 4: Update Brand and verify Brand in Addresses list
  describe('Update Brand and verify Brand in Addresses list', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateBrand', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', createBrandData.name);

      const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, brandsTable);
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      await expect(textColumn).to.contains(createBrandData.name);
    });

    it('should go to edit brand page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditBrandPage', baseContext);

      await brandsPage.goToEditBrandPage(page, 1);

      const pageTitle = await addBrandPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addBrandPage.pageTitleEdit);
    });

    it('should edit brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateBrand', baseContext);

      const result = await addBrandPage.createEditBrand(page, editBrandData);
      editBrandData.addresses += 1;
      await expect(result).to.equal(brandsPage.successfulUpdateMessage);
    });

    it('should check the updated Brand in Addresses list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesListAfterUpdate', baseContext);

      await brandsPage.filterAddresses(page, 'input', 'name', editBrandData.name);

      const textColumn = await brandsPage.getTextColumnFromTableAddresses(page, 1, 'name');
      await expect(textColumn).to.contains(editBrandData.name);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterUpdateBrand', baseContext);

      // Reset Filter Brands
      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, brandsTable);
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 1);

      // Reset Filter Brand Address
      const numberOfBrandsAddressesAfterReset = await brandsPage.resetAndGetNumberOfLines(page, addressesTable);
      await expect(numberOfBrandsAddressesAfterReset).to.be.equal(numberOfBrandsAddresses + 1);
    });
  });

  // 5: Update Address
  describe('Update Address', async () => {
    it('should filter Brand Address list by name of edited brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAddress', baseContext);

      await brandsPage.filterAddresses(page, 'input', 'name', editBrandData.name);

      const textColumn = await brandsPage.getTextColumnFromTableAddresses(page, 1, 'name');
      await expect(textColumn).to.contains(editBrandData.name);
    });

    it('should go to edit brand address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await brandsPage.goToEditBrandAddressPage(page, 1);

      const pageTitle = await addBrandPage.getPageTitle(page);
      await expect(pageTitle).to.contains(brandsPage.pageTitle);
    });

    it('should edit brand address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const result = await addBrandAddressPage.createEditBrandAddress(page, editBrandAddressData);
      await expect(result).to.equal(brandsPage.successfulUpdateMessage);
    });

    it('should reset Brand Addresses filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterUpdateAddress', baseContext);

      const numberOfBrandsAddressesAfterReset = await brandsPage.resetAndGetNumberOfLines(page, addressesTable);
      await expect(numberOfBrandsAddressesAfterReset).to.be.equal(numberOfBrandsAddresses + 1);
    });
  });
  // 6 : View Brand and check Address Value in list
  describe('View Brand and check Address Value in list', async () => {
    it('should filter Brand list by name of brand created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewUpdatedBrand', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', editBrandData.name);

      const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, brandsTable);
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      await expect(textColumn).to.contains(editBrandData.name);
    });

    it('should view brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedBrand', baseContext);

      await brandsPage.viewBrand(page, 1);

      const pageTitle = await viewBrandPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editBrandData.name);
    });

    it('should check existence of the associated address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressOnUpdatedBrand', baseContext);

      const numberOfAddressesInGrid = await viewBrandPage.getNumberOfAddressesInGrid(page);
      await expect(numberOfAddressesInGrid).to.equal(editBrandData.addresses);

      const textColumn = await viewBrandPage.getTextColumnFromTableAddresses(page, 1, 1);
      await expect(textColumn).to.contains(`${editBrandAddressData.firstName} ${editBrandAddressData.lastName}`);
    });

    it('should go back to brands Page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPageAfterViewEditedBrand', baseContext);

      await viewBrandPage.goToPreviousPage(page);

      const pageTitle = await brandsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(brandsPage.pageTitle);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterViewUpdatedBrand', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, brandsTable);
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands + 1);
    });
  });

  // 7 : Delete Brand and verify that Address has no Brand associated
  describe('Delete Brand and verify that Address has no Brand associated', async () => {
    it('should filter Brand list by name of edited brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteBrand', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', editBrandData.name);

      const numberOfBrandsAfterFilter = await brandsPage.getNumberOfElementInGrid(page, brandsTable);
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      await expect(textColumn).to.contains(editBrandData.name);
    });

    it('should delete brand', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteBrand', baseContext);

      const result = await brandsPage.deleteBrand(page, '1');
      await expect(result).to.be.equal(brandsPage.successfulDeleteMessage);
    });

    it('should check that the Brand Address is deleted successfully', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedBrandOnAddressList', baseContext);

      await brandsPage.filterAddresses(page, 'input', 'firstname', editBrandAddressData.firstName);
      await brandsPage.filterAddresses(page, 'input', 'lastname', editBrandAddressData.lastName);

      const textColumn = await brandsPage.getTextColumnFromTableAddresses(page, 1, 'name');
      await expect(textColumn).to.contains('--');
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetBrandsListAfterDelete', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, brandsTable);
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands);
    });
  });

  // 8 : Delete Address
  describe('Delete brand Address', async () => {
    it('should filter Brand Address list by firstName and lastName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteAddress', baseContext);

      await brandsPage.filterAddresses(page, 'input', 'firstname', editBrandAddressData.firstName);
      await brandsPage.filterAddresses(page, 'input', 'lastname', editBrandAddressData.lastName);

      const textColumnFirstName = await brandsPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      await expect(textColumnFirstName).to.contains(editBrandAddressData.firstName);

      const textColumnLastName = await brandsPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
      await expect(textColumnLastName).to.contains(editBrandAddressData.lastName);
    });

    it('should delete Brand Address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const result = await brandsPage.deleteBrandAddress(page, '1');
      await expect(result).to.be.equal(brandsPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAddressesListAfterDelete', baseContext);

      const numberOfBrandsAddressesAfterReset = await brandsPage.resetAndGetNumberOfLines(page, addressesTable);
      await expect(numberOfBrandsAddressesAfterReset).to.be.equal(numberOfBrandsAddresses);
    });
  });
});
