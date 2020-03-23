require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const AddressesPage = require('@pages/BO/customers/addresses');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_addresses_sortAddresses';

let browser;
let page;
let numberOfAddresses = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    addressesPage: new AddressesPage(page),
  };
};

// Sort addresses by id, firstname, lastname, address, post code, city and country
describe('Sort addresses', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to addresses page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.addressesLink,
    );
    const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);
    numberOfAddresses = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
    await expect(numberOfAddresses).to.be.above(0);
  });
  // Sort customers
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
    it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
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
