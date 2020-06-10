require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const {demoAddresses} = require('@data/demo/brands');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_filterBrandAddresses';

let browser;
let page;
let numberOfBrandsAddresses = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
  };
};

// Filter And Quick Edit Addresses
describe('Filter And Quick Edit Addresses', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go to brands page
  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.brandsAndSuppliersLink,
    );

    await this.pageObjects.brandsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all filters and get Number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfBrandsAddresses = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer_address');
    await expect(numberOfBrandsAddresses).to.be.above(0);
  });

  // 1 : Filter brands
  describe('Filter brands addresses', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_address',
            filterValue: demoAddresses.first.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: demoAddresses.first.brand,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterFirstName',
            filterType: 'input',
            filterBy: 'firstname',
            filterValue: demoAddresses.first.firstName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterLastName',
            filterType: 'input',
            filterBy: 'lastname',
            filterValue: demoAddresses.first.lastName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterPostCode',
            filterType: 'input',
            filterBy: 'postcode',
            filterValue: demoAddresses.first.postalCode,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCity',
            filterType: 'input',
            filterBy: 'city',
            filterValue: demoAddresses.first.city,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCountry',
            filterType: 'select',
            filterBy: 'country',
            filterValue: demoAddresses.first.country,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await this.pageObjects.brandsPage.filterAddresses(
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfBrandsAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid(
          'manufacturer_address',
        );

        await expect(numberOfBrandsAddressesAfterFilter).to.be.at.most(numberOfBrandsAddresses);

        for (let i = 1; i <= numberOfBrandsAddressesAfterFilter; i++) {
          const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableAddresses(i, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfBrandsAddressesAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines(
          'manufacturer_address',
        );

        await expect(numberOfBrandsAddressesAfterReset).to.equal(numberOfBrandsAddresses);
      });
    });
  });
});
