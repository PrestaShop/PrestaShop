require('module-alias/register');

const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');

// Importing pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const addBrandPage = require('@pages/BO/catalog/brands/add');
const addBrandAddressPage = require('@pages/BO/catalog/brands/addresses/add');

// Importing data
const BrandFaker = require('@data/faker/brand');
const BrandAddressFaker = require('@data/faker/brandAddress');

// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_paginationAndSortBrandsAndAddresses';

let browserContext;
let page;
let numberOfBrands = 0;
let numberOfAddresses = 0;

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
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.brandsAndSuppliersLink,
    );
    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await brandsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  it('should reset all filters and get number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterBrandsTable', baseContext);

    numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAddressesTable', baseContext);

    numberOfAddresses = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer_address');
    await expect(numberOfAddresses).to.be.above(0);
  });

  // 1 : Create 11 new brands
  const creationBrandTests = new Array(10).fill(0, 0, 10);
  creationBrandTests.forEach((test, index) => {
    describe(`Create brand n°${index + 1} in BO`, async () => {
      const createBrandData = new BrandFaker({name: `todelete${index}`});

      before(() => files.generateImage(createBrandData.logo));

      it('should go to add new brand page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewBrandPage${index}`, baseContext);

        await brandsPage.goToAddNewBrandPage(page);
        const pageTitle = await addBrandPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addBrandPage.pageTitle);
      });

      it('should create brand and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createBrand${index}`, baseContext);

        const result = await addBrandPage.createEditBrand(page, createBrandData);
        await expect(result).to.equal(brandsPage.successfulCreationMessage);

        const numberOfBrandsAfterCreation = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');
        await expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + 1 + index);
      });

      after(() => files.deleteFile(createBrandData.logo));
    });
  });

  // 2 : Pagination of brands table
  describe('Pagination next and previous of brands table', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsChangeItemNumberTo10', baseContext);

      const paginationNumber = await brandsPage.selectPaginationLimit(page, 'manufacturer', '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsClickOnNext', baseContext);

      const paginationNumber = await brandsPage.paginationNext(page, 'manufacturer');
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsClickOnPrevious', baseContext);

      const paginationNumber = await brandsPage.paginationPrevious(page, 'manufacturer');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'brandsChangeItemNumberTo50', baseContext);

      const paginationNumber = await brandsPage.selectPaginationLimit(page, 'manufacturer', '50');
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

          let nonSortedTable = await brandsPage.getAllRowsColumnContentBrandsTable(page, test.args.sortBy);

          await brandsPage.sortTableBrands(page, test.args.sortBy, test.args.sortDirection);
          let sortedTable = await brandsPage.getAllRowsColumnContentBrandsTable(page, test.args.sortBy);

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await brandsPage.sortArray(nonSortedTable, test.args.isFloat);

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

      await brandsPage.filterBrands(page, 'input', 'name', 'todelete');
      const textColumn = await brandsPage.getTextColumnFromTableBrands(page, 1, 'name');
      await expect(textColumn).to.contains('todelete');
    });

    it('should delete brands with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteBrands', baseContext);

      const deleteTextResult = await brandsPage.deleteWithBulkActions(page, 'manufacturer');
      await expect(deleteTextResult).to.be.equal(brandsPage.successfulDeleteMessage);
    });

    it('should reset brands filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteBrands', baseContext);

      const numberOfBrandsAfterReset = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
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

        await brandsPage.goToAddNewBrandAddressPage(page);
        const pageTitle = await addBrandAddressPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addBrandAddressPage.pageTitle);
      });

      it('should create address and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index}`, baseContext);

        const result = await addBrandAddressPage.createEditBrandAddress(page, createAddressData);
        await expect(result).to.equal(brandsPage.successfulCreationMessage);

        const numberOfBrandsAddressesAfterCreation = await brandsPage.getNumberOfElementInGrid(
          page,
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

      const paginationNumber = await brandsPage.selectPaginationLimit(page, 'manufacturer_address', '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesClickOnNext', baseContext);

      const paginationNumber = await brandsPage.paginationNext(page, 'manufacturer_address');
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesClickOnPrevious', baseContext);

      const paginationNumber = await brandsPage.paginationPrevious(page, 'manufacturer_address');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesChangeItemNumberTo50', baseContext);

      const paginationNumber = await brandsPage.selectPaginationLimit(page, 'manufacturer_address', '50');
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

          let nonSortedTable = await brandsPage.getAllRowsColumnContentAddressesTable(
            page,
            test.args.sortBy,
          );

          await brandsPage.sortTableAddresses(page, test.args.sortBy, test.args.sortDirection);

          let sortedTable = await brandsPage.getAllRowsColumnContentAddressesTable(page, test.args.sortBy);

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await brandsPage.sortArray(nonSortedTable, test.args.isFloat);

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

      await brandsPage.filterAddresses(page, 'input', 'city', 'todelete');
      const textColumn = await brandsPage.getTextColumnFromTableAddresses(page, 1, 'city');
      await expect(textColumn).to.contains('todelete');
    });

    it('should delete addresses with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddresses', baseContext);

      const deleteTextResult = await brandsPage.deleteWithBulkActions(page, 'manufacturer_address');
      await expect(deleteTextResult).to.be.equal(brandsPage.successfulDeleteMessage);
    });

    it('should reset addresses filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteAddresses', baseContext);

      const numberOfAddressesAfterReset = await brandsPage.resetAndGetNumberOfLines(
        page,
        'manufacturer_address',
      );
      await expect(numberOfAddressesAfterReset).to.be.equal(numberOfAddresses);
    });
  });
});
