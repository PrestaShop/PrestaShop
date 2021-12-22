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
const addressesPage = require('@pages/BO/customers/addresses');
const addAddressPage = require('@pages/BO/customers/addresses/add');

// Import data
const AddressFaker = require('@data/faker/address');

const baseContext = 'functional_BO_customers_addresses_paginationAndSortAddresses';

let browserContext;
let page;
let numberOfAddresses = 0;

/*
Create 11 addresses
Paginate between pages
Sort addresses by id, firstname, lastname, address, post code, city and country
Delete addresses with bulk actions
 */
describe('BO - Customers - Addresses : Pagination and sort addresses table', async () => {
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

  it('should go to \'Customers > Addresses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.addressesLink,
    );

    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await addressesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(addressesPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfAddresses = await addressesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfAddresses).to.be.above(0);
  });

  // 1 : Create 11 new addresses
  // 1 : Create 10 new addresses
  describe('Create 10 addresses in BO', async () => {
    const creationTests = new Array(10).fill(0, 0, 10);
    creationTests.forEach((test, index) => {
      const createAddressData = new AddressFaker(
        {
          email: 'pub@prestashop.com',
          address: `My address${index}`,
          country: 'France',
        },
      );

      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await addressesPage.goToAddNewAddressPage(page);
        const pageTitle = await addAddressPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addAddressPage.pageTitleCreate);
      });

      it(`should create address n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index}`, baseContext);

        const textResult = await addAddressPage.createEditAddress(page, createAddressData);
        await expect(textResult).to.equal(addressesPage.successfulCreationMessage);

        const numberOfAddressesAfterCreation = await addressesPage.getNumberOfElementInGrid(page);
        await expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1 + index);
      });
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await addressesPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await addressesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await addressesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await addressesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort addresses
  describe('Sort addresses table', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_address', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByFirstNameAsc', sortBy: 'firstname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByFirstNameDesc', sortBy: 'firstname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByLastNameAsc', sortBy: 'lastname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByLastNameDesc', sortBy: 'lastname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByAddress1Asc', sortBy: 'address1', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByAddress1Desc', sortBy: 'address1', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByPostCodeAsc', sortBy: 'postcode', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByPostCodeDesc', sortBy: 'postcode', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByCityAsc', sortBy: 'city', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByCityDesc', sortBy: 'city', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByCountryAsc', sortBy: 'country_name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByCountryDesc', sortBy: 'country_name', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_address', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await addressesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await addressesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await addressesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await addressesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete addresses created with bulk actions
  describe('Delete addresses with Bulk Actions', async () => {
    it('should filter list by address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await addressesPage.resetFilter(page);
      await addressesPage.filterAddresses(page, 'input', 'address1', 'My address');

      const address = await addressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
      await expect(address).to.contains('My address');
    });

    it('should delete addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAddresses', baseContext);

      const deleteTextResult = await addressesPage.deleteAddressesBulkActions(page);
      await expect(deleteTextResult).to.be.equal(addressesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfAddressesAfterReset = await addressesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfAddressesAfterReset).to.be.equal(numberOfAddresses);
    });
  });
});
