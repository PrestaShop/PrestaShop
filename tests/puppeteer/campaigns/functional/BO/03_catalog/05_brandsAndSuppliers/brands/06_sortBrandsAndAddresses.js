require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_sortBrandsAndAddresses';

let browser;
let page;
let numberOfBrands = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
  };
};

describe('Sort brands and addresses', async () => {
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

  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.brandsAndSuppliersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all filters and get Number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);
    numberOfBrands = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  describe('Sort Brands', async () => {
    const brandsTests = [
      {
        args:
          {
            testIdentifier: 'sortBrandsByIdBrandDesc', sortBy: 'id_manufacturer', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBrandsByNameAsc', sortBy: 'name', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBrandsByNameDesc', sortBy: 'name', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBrandsByIdBrandAsc', sortBy: 'id_manufacturer', sortDirection: 'asc', isFloat: true,
          },
      },
    ];
    brandsTests.forEach((test) => {
      it(
        `should sort Brands by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);
          let nonSortedTable = await this.pageObjects.brandsPage.getAllRowsColumnContentBrandsTable(test.args.sortBy);
          await this.pageObjects.brandsPage.sortTableBrands(test.args.sortBy, test.args.sortDirection);
          let sortedTable = await this.pageObjects.brandsPage.getAllRowsColumnContentBrandsTable(test.args.sortBy);
          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }
          const expectedResult = await this.pageObjects.brandsPage.sortArray(nonSortedTable, test.args.isFloat);
          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  describe('Sort Addresses', async () => {
    const brandsTests = [
      {
        args:
          {
            testIdentifier: 'sortAddressesByIdAddressDesc', sortBy: 'id_address', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByNameAsc', sortBy: 'name', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByNameDesc', sortBy: 'name', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByFirstNameAsc', sortBy: 'firstname', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByFirstNameDesc', sortBy: 'firstname', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByLastNameAsc', sortBy: 'lastname', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByLastNameDesc', sortBy: 'lastname', sortDirection: 'desc',
          },
      },
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
      {
        args:
          {
            testIdentifier: 'sortAddressesByCityAsc', sortBy: 'city', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByCityDesc', sortBy: 'city', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByCountryAsc', sortBy: 'country', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByCountryDesc', sortBy: 'country', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByIdAddressAsc', sortBy: 'id_address', sortDirection: 'asc', isFloat: true,
          },
      },
    ];
    brandsTests.forEach((test) => {
      it(
        `should sort Addresses by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);
          let nonSortedTable = await this.pageObjects.brandsPage.getAllRowsColumnContentAddressesTable(
            test.args.sortBy,
          );
          await this.pageObjects.brandsPage.sortTableAddresses(test.args.sortBy, test.args.sortDirection);
          let sortedTable = await this.pageObjects.brandsPage.getAllRowsColumnContentAddressesTable(test.args.sortBy);
          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }
          const expectedResult = await this.pageObjects.brandsPage.sortArray(nonSortedTable, test.args.isFloat);
          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });
});
