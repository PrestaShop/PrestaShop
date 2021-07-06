require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const addBrandAddressPage = require('@pages/BO/catalog/brands/addAddress');

// Import data
const BrandAddressFaker = require('@data/faker/brandAddress');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_addresses_sortPaginationAndBulkActions';

let browserContext;
let page;
let numberOfAddresses = 0;
const tableName = 'manufacturer_address';

/*
Create 11 Addresses
Paginate between pages
Sort Addresses table
Enable/Disable/Delete Addresses by bulk actions
 */
describe('BO - Catalog - Brands & Suppliers : Sort, pagination and bulk actions Addresses table', async () => {
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

  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
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

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAddressesTable', baseContext);

    numberOfAddresses = await brandsPage.resetAndGetNumberOfLines(page, tableName);
    await expect(numberOfAddresses).to.be.above(0);
  });

  // 1 : Create 10 new addresses
  const creationAddressTests = new Array(10).fill(0, 0, 10);
  describe('Create 10 new Addresses in BO', async () => {
    creationAddressTests.forEach((test, index) => {
      const createAddressData = new BrandAddressFaker({city: `todelete${index}`});

      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await brandsPage.goToAddNewBrandAddressPage(page);

        const pageTitle = await addBrandAddressPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addBrandAddressPage.pageTitle);
      });

      it(`should create address n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index}`, baseContext);

        const result = await addBrandAddressPage.createEditBrandAddress(page, createAddressData);
        await expect(result).to.equal(brandsPage.successfulCreationMessage);

        const numberOfAddressesAfterCreation = await brandsPage.getNumberOfElementInGrid(page, tableName);
        await expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1 + index);
      });
    });
  });

  // 2 : Pagination of addresses table
  describe('Pagination next and previous of Addresses table', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesChangeItemsNumberTo10', baseContext);

      const paginationNumber = await brandsPage.selectPaginationLimit(page, tableName, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesClickOnNext', baseContext);

      const paginationNumber = await brandsPage.paginationNext(page, tableName);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesClickOnPrevious', baseContext);

      const paginationNumber = await brandsPage.paginationPrevious(page, tableName);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesChangeItemsNumberTo50', baseContext);

      const paginationNumber = await brandsPage.selectPaginationLimit(page, tableName, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort addresses table
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
        `should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`,
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

  // 4 : Delete addresses with bulk actions
  describe('Delete created Addresses with bulk actions', async () => {
    it('should filter list by city', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteAddresses', baseContext);

      await brandsPage.filterAddresses(page, 'input', 'city', 'todelete');
      const textColumn = await brandsPage.getTextColumnFromTableAddresses(page, 1, 'city');
      await expect(textColumn).to.contains('todelete');
    });

    it('should delete with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddresses', baseContext);

      const deleteTextResult = await brandsPage.deleteWithBulkActions(page, tableName);
      await expect(deleteTextResult).to.be.equal(brandsPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteAddresses', baseContext);

      const numberOfAddressesAfterReset = await brandsPage.resetAndGetNumberOfLines(page, tableName);
      await expect(numberOfAddressesAfterReset).to.be.equal(numberOfAddresses);
    });
  });
});
