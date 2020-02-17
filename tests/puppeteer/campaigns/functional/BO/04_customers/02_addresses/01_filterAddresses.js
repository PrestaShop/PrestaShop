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
// Importing data
const Address = require('@data/demo/address');

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

// Filter addresses
describe('Filter Addresses', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to addresses page
  loginCommon.loginBO();

  it('should go to \'Customer>Addresses\' page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.addressesLink,
    );
    const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    numberOfAddresses = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
    await expect(numberOfAddresses).to.be.above(0);
  });
  // Filter addresses with all inputs and selects in grid table
  describe('Filter addresses', async () => {
    const tests = [
      {args: {filterType: 'input', filterBy: 'id_address', filterValue: Address.first.id}},
      {args: {filterType: 'input', filterBy: 'firstname', filterValue: Address.second.firstName}},
      {args: {filterType: 'input', filterBy: 'lastname', filterValue: Address.third.lastName}},
      {args: {filterType: 'input', filterBy: 'address1', filterValue: Address.first.address}},
      {args: {filterType: 'input', filterBy: 'postcode', filterValue: Address.second.zipCode}},
      {args: {filterType: 'input', filterBy: 'city', filterValue: Address.third.city}},
      {args: {filterType: 'select', filterBy: 'id_country', filterValue: Address.first.country}},
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await this.pageObjects.addressesPage.filterAddresses(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );
        const numberOfAddressesAfterFilter = await this.pageObjects.addressesPage.getNumberOfElementInGrid();
        await expect(numberOfAddressesAfterFilter).to.be.at.most(numberOfAddresses);
        for (let i = 1; i <= numberOfAddressesAfterFilter; i++) {
          const textColumn = await this.pageObjects.addressesPage.getTextColumnFromTableAddresses(
            i,
            test.args.filterBy === 'id_country' ? 'country_name' : test.args.filterBy,
          );
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        const numberOfAddressesAfterReset = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
        await expect(numberOfAddressesAfterReset).to.equal(numberOfAddresses);
      });
    });
  });
});
