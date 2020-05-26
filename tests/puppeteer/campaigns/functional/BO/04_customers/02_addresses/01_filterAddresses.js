require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const AddressesPage = require('@pages/BO/customers/addresses');

// Import data
const Address = require('@data/demo/address');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_addresses_filterAddresses';

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
  };
};

// Filter addresses
describe('Filter Addresses', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to addresses page
  loginCommon.loginBO();

  it('should go to \'Customer>Addresses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.customersParentLink,
      this.pageObjects.dashboardPage.addressesLink,
    );

    const pageTitle = await this.pageObjects.addressesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.addressesPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfAddresses = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
    await expect(numberOfAddresses).to.be.above(0);
  });

  // Filter addresses with all inputs and selects in grid table
  describe('Filter addresses', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_address',
            filterValue: Address.first.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterFirstName',
            filterType: 'input',
            filterBy: 'firstname',
            filterValue: Address.second.firstName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLanstName',
            filterType: 'input',
            filterBy: 'lastname',
            filterValue: Address.third.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterAddress',
            filterType: 'input',
            filterBy: 'address1',
            filterValue: Address.first.address,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterPostCode',
            filterType: 'input',
            filterBy: 'postcode',
            filterValue: Address.second.zipCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCity',
            filterType: 'input',
            filterBy: 'city',
            filterValue: Address.third.city,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterIdCountry',
            filterType: 'select',
            filterBy: 'id_country',
            filterValue: Address.first.country,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}`, baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfAddressesAfterReset = await this.pageObjects.addressesPage.resetAndGetNumberOfLines();
        await expect(numberOfAddressesAfterReset).to.equal(numberOfAddresses);
      });
    });
  });
});
