require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const MonitoringPage = require('@pages/BO/catalog/monitoring');
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_monitoring_monitoringProducts';

let browser;
let page;

let numberOfProducts = 0;
let numberOfProductsIngrid = 0;

// Init data
const productWithoutImage = new ProductFaker({type: 'Standard product'});
const disabledProduct = new ProductFaker({type: 'Standard product', status: false});
const productWithoutCombinationsWithoutQuantity = new ProductFaker({type: 'Standard product', quantity: 0});
const productWithCombinationsWithoutQuantity = new ProductFaker({type: 'Standard product', quantity: 0});
const productWithoutPrice = new ProductFaker({type: 'Standard product', price: 0});
const productWithoutDescription = new ProductFaker({type: 'Standard product', description: '', summary: ''});

// Init objects needed
const init = async function () {
  return {
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
      args:
        {
          testIdentifier: 'productWithoutImage',
          productType: 'without image',
          productToCreate: productWithoutImage,
          gridName: 'product_without_image',
        },
    },
    {
      args:
        {
          testIdentifier: 'disabledProduct',
          productType: 'disabled',
          productToCreate: disabledProduct,
          gridName: 'disabled_product',
        },
    },
    {
      args:
        {
          testIdentifier: 'productWithoutCombinationsWithoutQuantity',
          productType: 'without combinations and without available quantities',
          productToCreate: productWithoutCombinationsWithoutQuantity,
          gridName: 'no_qty_product_without_combination',
        },
    },
    {
      args: {
        testIdentifier: 'productWithCombinationsWithQuantity',
        productType: 'with combinations and without available quantities',
        productToCreate: productWithCombinationsWithoutQuantity,
        gridName: 'no_qty_product_with_combination',
        hasCombinations: true,
      },
    },
    {
      args: {
        testIdentifier: 'productWithoutPrice',
        productType: 'without price',
        productToCreate: productWithoutPrice,
        gridName: 'product_without_price',
      },
    },
    {
      args: {
        testIdentifier: 'productWithoutDescription',
        productType: 'without description',
        productToCreate: productWithoutDescription,
        gridName: 'product_without_description',
      },
    },
  ];

  tests.forEach((test) => {
    describe(`Create product ${test.args.productType} in BO`, async () => {
      it('should go to catalog > products page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_goToProductsPage`,
          baseContext,
        );

        await this.pageObjects.dashboardPage.goToSubMenu(
          this.pageObjects.dashboardPage.catalogParentLink,
          this.pageObjects.dashboardPage.productsLink,
        );

        await this.pageObjects.productsPage.closeSfToolBar();

        const pageTitle = await this.pageObjects.productsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
      });

      it('should reset all filters and get number of products in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}_resetFirst`, baseContext);

        numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
        await expect(numberOfProducts).to.be.above(0);
      });

      it('should create product and check the products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}_create`, baseContext);

        await this.pageObjects.productsPage.goToAddProductPage();
        let createProductMessage = await this.pageObjects.addProductPage.createEditBasicProduct(
          test.args.productToCreate,
        );

        if (test.args.hasCombinations) {
          createProductMessage = await this.pageObjects.addProductPage.setCombinationsInProduct(
            test.args.productToCreate,
          );
        }

        await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
      });
    });

    describe('Check created product in monitoring page', async () => {
      it('should go to catalog > monitoring page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_goToMonitoringPage`,
          baseContext,
        );

        await this.pageObjects.addProductPage.goToSubMenu(
          this.pageObjects.addProductPage.catalogParentLink,
          this.pageObjects.addProductPage.monitoringLink,
        );

        const pageTitle = await this.pageObjects.monitoringPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.monitoringPage.pageTitle);

        numberOfProductsIngrid = await this.pageObjects.monitoringPage.resetAndGetNumberOfLines(
          test.args.gridName,
        );

        await expect(numberOfProductsIngrid).to.be.at.least(1);
      });

      it(`should filter products ${test.args.productType} grid and check existence of new product`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_checkProduct`,
          baseContext,
        );

        await this.pageObjects.monitoringPage.filterTable(
          test.args.gridName,
          'input',
          'name',
          test.args.productToCreate.name,
        );

        const textColumn = await this.pageObjects.monitoringPage.getTextColumnFromTable(
          test.args.gridName,
          1,
          'name',
        );

        await expect(textColumn).to.contains(test.args.productToCreate.name);
      });

      it(`should reset filter in products ${test.args.productType} grid`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_resetInMonitoringPage`,
          baseContext,
        );

        numberOfProductsIngrid = await this.pageObjects.monitoringPage.resetAndGetNumberOfLines(test.args.gridName);
        await expect(numberOfProductsIngrid).to.be.at.least(1);
      });
    });

    describe('Delete product from monitoring page', async () => {
      it(`should filter products ${test.args.productType} grid`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_filterToDelete`,
          baseContext,
        );

        await this.pageObjects.monitoringPage.filterTable(
          test.args.gridName,
          'input',
          'name',
          test.args.productToCreate.name,
        );

        const textColumn = await this.pageObjects.monitoringPage.getTextColumnFromTable(
          test.args.gridName,
          1,
          'name',
        );

        await expect(textColumn).to.contains(test.args.productToCreate.name);
      });

      it('should delete product', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_deleteProduct`,
          baseContext,
        );

        const textResult = await this.pageObjects.monitoringPage.deleteProductInGrid(test.args.gridName, 1);
        await expect(textResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);

        const pageTitle = await this.pageObjects.productsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
      });

      it('should reset filter check number of products', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_resetInProductsPage`,
          baseContext,
        );

        const numberOfProductsAfterDelete = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
        await expect(numberOfProductsAfterDelete).to.be.equal(numberOfProducts);
      });
    });
  });
});
