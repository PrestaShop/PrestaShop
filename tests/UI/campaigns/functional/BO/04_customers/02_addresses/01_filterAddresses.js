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

// Import data
const Address = require('@data/demo/address');

const baseContext = 'functional_BO_customers_addresses_filterAddresses';

let browserContext;
let page;
let numberOfAddresses = 0;

/*
Filter addresses table by Id, firstname, lastname, address, postcode, city and country
 */
describe('BO - Customers - Addresses : Filter Addresses table', async () => {
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

  it('should go to \'Customer > Addresses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.addressesLink,
    );

    const pageTitle = await addressesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(addressesPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfAddresses = await addressesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfAddresses).to.be.above(0);
  });

  // Filter addresses with all inputs and selects in grid table
  describe('Filter addresses table', async () => {
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

        await addressesPage.filterAddresses(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfAddressesAfterFilter = await addressesPage.getNumberOfElementInGrid(page);
        await expect(numberOfAddressesAfterFilter).to.be.at.most(numberOfAddresses);

        for (let i = 1; i <= numberOfAddressesAfterFilter; i++) {
          const textColumn = await addressesPage.getTextColumnFromTableAddresses(
            page,
            i,
            test.args.filterBy === 'id_country' ? 'country_name' : test.args.filterBy,
          );
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfAddressesAfterReset = await addressesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfAddressesAfterReset).to.equal(numberOfAddresses);
      });
    });
  });
});
