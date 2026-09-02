// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boBrandsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataBrandAddresses,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_brands_addresses_filterAddresses';

// Filter and quick edit Addresses
describe('BO - Catalog - Brands & Suppliers : Filter and quick edit Addresses table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAddresses: number = 0;

  const tableName: string = 'manufacturer_address';

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

  // Go to brands page
  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.brandsAndSuppliersLink,
    );
    await boBrandsPage.closeSfToolBar(page);

    const pageTitle = await boBrandsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boBrandsPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfAddresses = await boBrandsPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfAddresses).to.be.above(0);
  });

  // 1 : Filter addresses table
  describe('Filter Addresses table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_address',
            filterValue: dataBrandAddresses.brandAddress_4.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: dataBrandAddresses.brandAddress_4.brandName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterFirstName',
            filterType: 'input',
            filterBy: 'firstname',
            filterValue: dataBrandAddresses.brandAddress_4.firstName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLastName',
            filterType: 'input',
            filterBy: 'lastname',
            filterValue: dataBrandAddresses.brandAddress_4.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterPostCode',
            filterType: 'input',
            filterBy: 'postcode',
            filterValue: dataBrandAddresses.brandAddress_4.postalCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCity',
            filterType: 'input',
            filterBy: 'city',
            filterValue: dataBrandAddresses.brandAddress_4.city,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCountry',
            filterType: 'select',
            filterBy: 'country',
            filterValue: dataBrandAddresses.brandAddress_4.country,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boBrandsPage.filterAddresses(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfAddressesAfterFilter = await boBrandsPage.getNumberOfElementInGrid(page, tableName);
        expect(numberOfAddressesAfterFilter).to.be.at.most(numberOfAddresses);

        for (let i = 1; i <= numberOfAddressesAfterFilter; i++) {
          const textColumn = await boBrandsPage.getTextColumnFromTableAddresses(page, i, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfAddressesAfterReset = await boBrandsPage.resetAndGetNumberOfLines(page, tableName);
        expect(numberOfAddressesAfterReset).to.equal(numberOfAddresses);
      });
    });
  });
});
