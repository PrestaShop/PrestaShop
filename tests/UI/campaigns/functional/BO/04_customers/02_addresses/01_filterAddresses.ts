// Import utils
import testContext from '@utils/testContext';

// Import pages
import addressesPage from '@pages/BO/customers/addresses';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataAddresses,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_addresses_filterAddresses';

/*
Filter addresses table by Id, firstname, lastname, address, postcode, city and country
 */
describe('BO - Customers - Addresses : Filter Addresses table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAddresses: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Customer > Addresses\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.addressesLink,
    );

    const pageTitle = await addressesPage.getPageTitle(page);
    expect(pageTitle).to.contains(addressesPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfAddresses = await addressesPage.resetAndGetNumberOfLines(page);
    expect(numberOfAddresses).to.be.above(0);
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
            filterValue: dataAddresses.address_1.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterFirstName',
            filterType: 'input',
            filterBy: 'firstname',
            filterValue: dataAddresses.address_2.firstName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLanstName',
            filterType: 'input',
            filterBy: 'lastname',
            filterValue: dataAddresses.address_5.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterAddress',
            filterType: 'input',
            filterBy: 'address1',
            filterValue: dataAddresses.address_1.address,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterPostCode',
            filterType: 'input',
            filterBy: 'postcode',
            filterValue: dataAddresses.address_2.postalCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCity',
            filterType: 'input',
            filterBy: 'city',
            filterValue: dataAddresses.address_5.city,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterIdCountry',
            filterType: 'select',
            filterBy: 'id_country',
            filterValue: dataAddresses.address_1.country,
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
        expect(numberOfAddressesAfterFilter).to.be.at.most(numberOfAddresses);

        for (let i = 1; i <= numberOfAddressesAfterFilter; i++) {
          const textColumn = await addressesPage.getTextColumnFromTableAddresses(
            page,
            i,
            test.args.filterBy === 'id_country' ? 'country_name' : test.args.filterBy,
          );
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfAddressesAfterReset = await addressesPage.resetAndGetNumberOfLines(page);
        expect(numberOfAddressesAfterReset).to.equal(numberOfAddresses);
      });
    });
  });
});
