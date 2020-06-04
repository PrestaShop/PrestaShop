require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const AddressesPage = require('@pages/BO/customers/addresses');
const AddAddressPage = require('@pages/BO/customers/addresses/add');

// Import data
const AddressFaker = require('@data/faker/address');
// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_addresses_paginationAndSortAddresses';

let browser;
let browserContext;
let page;
let numberOfAddresses = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    addressesPage: new AddressesPage(page),
    addAddressPage: new AddAddressPage(page),
  };
};
/*
Create 11 addresses
Paginate between pages
Sort addresses by id, firstname, lastname, address, post code, city and country
Delete addresses with bulk actions
 */
describe('Pagination and sort addresses', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to addresses page
  loginCommon.loginBO();

  it('should go to addresses page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.customersParentLink,
      this.pageObjects.dashboardPage.addressesLink,
    );

    await this.pageObjects.dashboardPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfAddresses = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
    await expect(numberOfAddresses).to.be.above(0);
  });

  // 1 : Create 11 new addresses
  const creationTests = new Array(10).fill(0, 0, 10);
  creationTests.forEach((test, index) => {
    describe(`Create address n°${index + 1} in BO`, async () => {
      const createAddressData = new AddressFaker(
        {
          email: 'pub@prestashop.com',
          address: `My address${index}`,
          country: 'France',
        },
      );

      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await this.pageObjects.addressesPage.goToAddNewAddressPage();
        const pageTitle = await this.pageObjects.addAddressPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addAddressPage.pageTitleCreate);
      });

      it('should create address and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index}`, baseContext);

        const textResult = await this.pageObjects.addAddressPage.createEditAddress(createAddressData);
        await expect(textResult).to.equal(this.pageObjects.addressesPage.successfulCreationMessage);

        const numberOfAddressesAfterCreation = await this.pageObjects.addressesPage.getNumberOfElementInGrid();
        await expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1 + index);
      });
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await this.pageObjects.addressesPage.selectPaginationLimit('10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await this.pageObjects.addressesPage.paginationNext();
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await this.pageObjects.addressesPage.paginationPrevious();
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await this.pageObjects.addressesPage.selectPaginationLimit('50');
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

        let nonSortedTable = await this.pageObjects.addressesPage.getAllRowsColumnContent(test.args.sortBy);

        await this.pageObjects.addressesPage.sortTable(test.args.sortBy, test.args.sortDirection);

        let sortedTable = await this.pageObjects.addressesPage.getAllRowsColumnContent(test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await this.pageObjects.addressesPage.sortArray(nonSortedTable, test.args.isFloat);

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

      await this.pageObjects.addressesPage.resetFilter();
      await this.pageObjects.addressesPage.filterAddresses('input', 'address1', 'My address');

      const address = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(1, 'address1');
      await expect(address).to.contains('My address');
    });

    it('should delete addresses with Bulk Actions and check addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAddresses', baseContext);

      const deleteTextResult = await this.pageObjects.addressesPage.deleteAddressesBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.addressesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfAddressesAfterReset = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
      await expect(numberOfAddressesAfterReset).to.be.equal(numberOfAddresses);
    });
  });
});
