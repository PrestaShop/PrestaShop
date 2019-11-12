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
const ProductsPage = require('@pages/BO/catalog/products/index');

let browser;
let page;
let numberOfProducts = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productsPage: new ProductsPage(page),
  };
};

// Filter Products
describe('Filter Products', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  /*after(async () => {
    await helper.closeBrowser(browser);
  });*/

  // Login into BO and go to products page
  loginCommon.loginBO();

  it('should go to "Catalog>products" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters and get Number of products in BO', async function () {
    numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });
  // 1 : Filter products with all inputs and selects in grid table
  describe('Filter products', async () => {
    it('should filter by Id MIN-MAX \'2-10\'', async function () {
      await this.pageObjects.productsPage.filterIDProducts(2, 10);
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const productID = await this.pageObjects.productsPage.getProductIDFromList(i);
        await expect(productID).to.within(2, 10);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should filter by Name \'Customizable mug\'', async function () {
      await this.pageObjects.productsPage.filterProducts('name', Products.demo_14.name);
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await this.pageObjects.productsPage.getProductNameFromList(i);
        await expect(textColumn).to.contains(Products.demo_14.name);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should filter by reference', async function () {
      await this.pageObjects.productsPage.filterProducts(
        'reference',
        Products.demo_3.reference,
      );
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await this.pageObjects.productsPage.getProductReferenceFromList(i);
        await expect(textColumn).to.contains(Products.demo_3.reference);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should filter by Category \'Art\'', async function () {
      await this.pageObjects.productsPage.filterProducts(
        'name_category',
        Products.demo_5.category,
      );
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await this.pageObjects.productsPage.getProductCategoryFromList(i);
        await expect(textColumn).to.contains(Products.demo_5.category);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should filter by Price(tax excl.) MIN-MAX \'13-20\'', async function () {
      await this.pageObjects.productsPage.filterPriceProducts(13, 20);
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const productID = await this.pageObjects.productsPage.getProductPriceFromList(i);
        await expect(productID).to.be.within(13, 20);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should filter by Quantity MIN-MAX \'900-1500\'', async function () {
      await this.pageObjects.productsPage.filterQuantityProducts(900, 1500);
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const productID = await this.pageObjects.productsPage.getProductQuantityFromList(i);
        await expect(productID).to.be.within(900, 1500);
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should reset all filters', async function () {
      const numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should filter by Status \'Active\'', async function () {
      await this.pageObjects.productsPage.filterProducts(
        'active',
        'Active',
        'select',
      );
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const status = await this.pageObjects.productsPage.getProductStatusFromList(i);
        await expect(status).to.be.equal('check');
      }
      /* eslint-enable no-await-in-loop */
    });
  });
});
