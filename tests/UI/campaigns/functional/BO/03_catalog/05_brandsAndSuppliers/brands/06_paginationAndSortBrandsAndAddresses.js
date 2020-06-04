require('module-alias/register');

const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');
// Importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');
const AddBrandPage = require('@pages/BO/catalog/brands/add');
const AddBrandAddressPage = require('@pages/BO/catalog/brands/addresses/add');
// Importing data
const BrandFaker = require('@data/faker/brand');
const BrandAddressFaker = require('@data/faker/brandAddress');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_paginationAndSortBrandsAndAddresses';

let browser;
let browserContext;
let page;
let numberOfBrands = 0;
let numberOfAddresses = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    addBrandPage: new AddBrandPage(page),
    addBrandAddressPage: new AddBrandAddressPage(page),
  };
};
/*
Create 11 brands/Addresses
Paginate between pages
Sort brands/addresses table
Delete brands/addresses with bulk actions
 */
describe('Pagination and sort brands and addresses', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.brandsAndSuppliersLink,
    );
    await this.pageObjects.dashboardPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all filters and get number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBrandsTable', baseContext);

    numberOfBrands = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAddressesTable', baseContext);

    numberOfAddresses = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer_address');
    await expect(numberOfAddresses).to.be.above(0);
  });

  // 1 : Create 11 new brands
  const creationBrandTests = new Array(10).fill(0, 0, 10);
  creationBrandTests.forEach((test, index) => {
    describe(`Create brand n°${index + 1} in BO`, async () => {
      const createBrandData = new BrandFaker({name: `todelete${index}`});

      it('should go to add new brand page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewBrandPage${index}`, baseContext);

        await this.pageObjects.brandsPage.goToAddNewBrandPage();
        const pageTitle = await this.pageObjects.addBrandPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addBrandPage.pageTitle);
      });

      it('should create brand and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createBrand${index}`, baseContext);

        const result = await this.pageObjects.addBrandPage.createEditBrand(createBrandData);
        await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);

        const numberOfBrandsAfterCreation = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
        await expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + 1 + index);
      });

      after(() => files.deleteFile(createBrandData.logo));
    });
  });

  // 2 : Pagination of brands table
  describe('Pagination next and previous of brands table', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsChangeItemNumberTo10', baseContext);

      const paginationNumber = await this.pageObjects.brandsPage.selectPaginationLimit('manufacturer', '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsClickOnNext', baseContext);

      const paginationNumber = await this.pageObjects.brandsPage.paginationNext('manufacturer');
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsClickOnPrevious', baseContext);

      const paginationNumber = await this.pageObjects.brandsPage.paginationPrevious('manufacturer');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsChangeItemNumberTo50', baseContext);

      const paginationNumber = await this.pageObjects.brandsPage.selectPaginationLimit('manufacturer', '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3: sort brands
  describe('Sort Brands', async () => {
    const brandsTests = [
      {
        args:
          {
            testIdentifier: 'sortBrandsByIdBrandDesc', sortBy: 'id_manufacturer', sortDirection: 'desc', isFloat: true,
          },
      },
      {args: {testIdentifier: 'sortBrandsByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortBrandsByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {
        args:
          {
            testIdentifier: 'sortBrandsByIdBrandAsc', sortBy: 'id_manufacturer', sortDirection: 'asc', isFloat: true,
          },
      },
    ];
    brandsTests.forEach((test) => {
      it(
        `should sort Brands by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          let nonSortedTable = await this.pageObjects.brandsPage.getAllRowsColumnContentBrandsTable(test.args.sortBy);
          await this.pageObjects.brandsPage.sortTableBrands(test.args.sortBy, test.args.sortDirection);
          let sortedTable = await this.pageObjects.brandsPage.getAllRowsColumnContentBrandsTable(test.args.sortBy);
          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }
          const expectedResult = await this.pageObjects.brandsPage.sortArray(nonSortedTable, test.args.isFloat);
          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  // 4 : Delete brands with bulk actions
  describe('Delete brands with bulk actions', async () => {
    it('should filter brands list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteBrands', baseContext);

      await this.pageObjects.brandsPage.filterBrands('input', 'name', 'todelete');
      const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableBrands(1, 'name');
      await expect(textColumn).to.contains('todelete');
    });

    it('should delete brands with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteBrands', baseContext);

      const deleteTextResult = await this.pageObjects.brandsPage.deleteWithBulkActions('manufacturer');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });

    it('should reset brands filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteBrands', baseContext);

      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
      await expect(numberOfBrandsAfterReset).to.be.equal(numberOfBrands);
    });
  });

  // 5 : Create 11 new addresses
  const creationAddressTests = new Array(10).fill(0, 0, 10);
  creationAddressTests.forEach((test, index) => {
    describe(`Create address n°${index + 1} in BO`, async () => {
      const createAddressData = new BrandAddressFaker({city: `todelete${index}`});

      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await this.pageObjects.brandsPage.goToAddNewBrandAddressPage();
        const pageTitle = await this.pageObjects.addBrandAddressPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addBrandAddressPage.pageTitle);
      });

      it('should create address and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index}`, baseContext);

        const result = await this.pageObjects.addBrandAddressPage.createEditBrandAddress(createAddressData);
        await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);

        const numberOfBrandsAddressesAfterCreation = await this.pageObjects.brandsPage.getNumberOfElementInGrid(
          'manufacturer_address',
        );
        await expect(numberOfBrandsAddressesAfterCreation).to.be.equal(numberOfAddresses + 1 + index);
      });
    });
  });

  // 6 : Pagination of addresses table
  describe('Pagination next and previous of addresses table', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesChangeItemNumberTo10', baseContext);

      const paginationNumber = await this.pageObjects.brandsPage.selectPaginationLimit('manufacturer_address', '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesClickOnNext', baseContext);

      const paginationNumber = await this.pageObjects.brandsPage.paginationNext('manufacturer_address');
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesClickOnPrevious', baseContext);

      const paginationNumber = await this.pageObjects.brandsPage.paginationPrevious('manufacturer_address');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesChangeItemNumberTo50', baseContext);

      const paginationNumber = await this.pageObjects.brandsPage.selectPaginationLimit('manufacturer_address', '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 7 : Sort addresses table
  describe('Sort Addresses', async () => {
    const brandsTests = [
      {
        args:
          {
            testIdentifier: 'sortAddressesByIdAddressDesc', sortBy: 'id_address', sortDirection: 'desc', isFloat: true,
          },
      },
      {args: {testIdentifier: 'sortAddressesByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortAddressesByFirstNameAsc', sortBy: 'firstname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByFirstNameDesc', sortBy: 'firstname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortAddressesByLastNameAsc', sortBy: 'lastname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByLastNameDesc', sortBy: 'lastname', sortDirection: 'desc'}},
      {
        args:
          {
            testIdentifier: 'sortAddressesByPostCodeAsc', sortBy: 'postcode', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByPostCodeDesc', sortBy: 'postcode', sortDirection: 'desc', isFloat: true,
          },
      },
      {args: {testIdentifier: 'sortAddressesByCityAsc', sortBy: 'city', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByCityDesc', sortBy: 'city', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortAddressesByCountryAsc', sortBy: 'country', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByCountryDesc', sortBy: 'country', sortDirection: 'desc'}},
      {
        args:
          {
            testIdentifier: 'sortAddressesByIdAddressAsc', sortBy: 'id_address', sortDirection: 'asc', isFloat: true,
          },
      },
    ];
    brandsTests.forEach((test) => {
      it(
        `should sort Addresses by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          let nonSortedTable = await this.pageObjects.brandsPage.getAllRowsColumnContentAddressesTable(
            test.args.sortBy,
          );

          await this.pageObjects.brandsPage.sortTableAddresses(test.args.sortBy, test.args.sortDirection);

          let sortedTable = await this.pageObjects.brandsPage.getAllRowsColumnContentAddressesTable(test.args.sortBy);

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await this.pageObjects.brandsPage.sortArray(nonSortedTable, test.args.isFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  // 8 : Delete addresses with bulk actions
  describe('Delete addresses with bulk actions', async () => {
    it('should filter address list by city', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteAddresses', baseContext);

      await this.pageObjects.brandsPage.filterAddresses('input', 'city', 'todelete');
      const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableAddresses(1, 'city');
      await expect(textColumn).to.contains('todelete');
    });

    it('should delete addresses with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddresses', baseContext);

      const deleteTextResult = await this.pageObjects.brandsPage.deleteWithBulkActions('manufacturer_address');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });

    it('should reset addresses filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteAddresses', baseContext);

      const numberOfAddressesAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines(
        'manufacturer_address',
      );
      await expect(numberOfAddressesAfterReset).to.be.equal(numberOfAddresses);
    });
  });
});
