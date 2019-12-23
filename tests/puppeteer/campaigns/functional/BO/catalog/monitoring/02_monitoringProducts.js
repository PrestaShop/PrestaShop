require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const MonitoringPage = require('@pages/BO/catalog/monitoring');
const ProductFaker = require('@data/faker/product');

let browser;
let page;
let numberOfProducts = 0;
let numberOfProductsIngrid = 0;
const productWithoutCombinations = new ProductFaker(
  {type: 'Standard product', productHasCombinations: false},
);
const productWithoutCombinationsWithoutQuantity = new ProductFaker(
  {type: 'Standard product', productHasCombinations: false, quantity: '0'},
);
const productWithCombinationsWithoutQuantity = new ProductFaker(
  {type: 'Standard product', productHasCombinations: true, quantity: '0'},
);
const productWithoutPrice = new ProductFaker(
  {type: 'Standard product', productHasCombinations: false, price: '0'},
);

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    monitoringPage: new MonitoringPage(page),
  };
};

/*
Create new product
Check existence of new product in monitoring page
Delete product and check deletion in products page
 */
describe('Create different products and delete them from monitoring page', async () => {
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

  const tests = [
    {
      args: {
        productType: 'without image',
        productToCreate: productWithoutCombinations,
        gridToVerify: 'product_without_image',
        enabled: true,
      },
    },
    {
      args: {
        productType: 'disabled',
        productToCreate: productWithoutCombinations,
        gridToVerify: 'disabled_product',
        enabled: false,
      },
    },
    {
      args: {
        productType: 'without combinations and without available quantities',
        productToCreate: productWithoutCombinationsWithoutQuantity,
        gridToVerify: 'no_qty_product_without_combination',
        enabled: true,
      },
    },
    {
      args: {
        productType: 'with combinations and without available quantities',
        productToCreate: productWithCombinationsWithoutQuantity,
        gridToVerify: 'no_qty_product_with_combination',
        enabled: true,
      },
    },
    {
      args: {
        productType: 'without price',
        productToCreate: productWithoutPrice,
        gridToVerify: 'product_without_price',
        enabled: true,
      },
    },
  ];
  tests.forEach((test) => {
    describe(`Create product ${test.args.productType} in BO`, async () => {
      it('should go to catalog > products page', async function () {
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.catalogParentLink,
          this.pageObjects.boBasePage.productsLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.productsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
      });

      it('should reset all filters and get number of products in BO', async function () {
        numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
        await expect(numberOfProducts).to.be.above(0);
      });

      it('should create product and check the products number', async function () {
        await this.pageObjects.productsPage.goToAddProductPage();
        const createProductMessage = await this.pageObjects.addProductPage.createEditProduct(
          test.args.productToCreate,
          test.args.enabled,
        );
        await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
      });
    });

    describe('Check created product in monitoring page', async () => {
      it('should go to catalog > monitoring page', async function () {
        await this.pageObjects.addProductPage.goToSubMenu(
          this.pageObjects.boBasePage.catalogParentLink,
          this.pageObjects.boBasePage.monitoringLink,
        );
        const pageTitle = await this.pageObjects.monitoringPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.monitoringPage.pageTitle);
        numberOfProductsIngrid = await this.pageObjects.monitoringPage.resetAndGetNumberOfLines(
          test.args.gridToVerify,
        );
        await expect(numberOfProductsIngrid).to.be.at.least(1);
      });

      it(`should filter products ${test.args.productType} grid and check existence of new product`, async function () {
        await this.pageObjects.monitoringPage.filterTable(
          test.args.gridToVerify,
          'input',
          'name',
          test.args.productToCreate.name,
        );
        const textColumn = await this.pageObjects.monitoringPage.getTextColumnFromTable(
          test.args.gridToVerify,
          1,
          'name',
        );
        await expect(textColumn).to.contains(test.args.productToCreate.name);
      });

      it(`should reset filter in products ${test.args.productType} grid`, async function () {
        numberOfProductsIngrid = await this.pageObjects.monitoringPage.resetAndGetNumberOfLines(test.args.gridToVerify);
        await expect(numberOfProductsIngrid).to.be.at.least(1);
      });
    });

    describe('Delete product from monitoring page', async () => {
      it(`should filter products ${test.args.productType} grid`, async function () {
        await this.pageObjects.monitoringPage.filterTable(
          test.args.gridToVerify,
          'input',
          'name',
          test.args.productToCreate.name,
        );
        const textColumn = await this.pageObjects.monitoringPage.getTextColumnFromTable(
          test.args.gridToVerify,
          1,
          'name',
        );
        await expect(textColumn).to.contains(test.args.productToCreate.name);
      });

      it('should delete product', async function () {
        const textResult = await this.pageObjects.monitoringPage.deleteProductInGrid(test.args.gridToVerify, 1);
        await expect(textResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
        const pageTitle = await this.pageObjects.productsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
      });

      it('should reset filter check number of products', async function () {
        const numberOfProductsAfterDelete = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
        await expect(numberOfProductsAfterDelete).to.be.equal(numberOfProducts);
      });
    });
  });
});
