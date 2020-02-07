require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Products} = require('@data/demo/products');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const StocksPage = require('@pages/BO/catalog/stocks');

let browser;
let page;
let numberOfProducts = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    stocksPage: new StocksPage(page),
  };
};

// Simple filter stocks
describe('Simple filter stocks', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to stocks page
  loginCommon.loginBO();

  it('should go to "Catalog>Stocks" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.stocksLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.stocksPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.stocksPage.pageTitle);
  });

  it('should get number of products in list', async function () {
    numberOfProducts = await this.pageObjects.stocksPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });

  // Filter products with name, reference, supplier
  describe('Filter products with name, reference, supplier', async () => {
    const tests = [
      {args: {filterBy: 'name', filterValue: Products.demo_1.name}},
      {args: {filterBy: 'reference', filterValue: Products.demo_1.reference}},
      {args: {filterBy: 'supplier', filterValue: 'N/A'}},
    ];
    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await this.pageObjects.stocksPage.simpleFilter(test.args.filterValue);
        const numberOfProductsAfterFilter = await this.pageObjects.stocksPage.getNumberOfProductsFromList();
        await expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);
        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          const textColumn = await this.pageObjects.stocksPage.getTextColumnFromTableStocks(i, test.args.filterBy);
          await expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        const numberOfProductsAfterReset = await this.pageObjects.stocksPage.resetFilter();
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });
});
