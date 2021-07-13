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
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const monitoringPage = require('@pages/BO/catalog/monitoring');

// Import data
const ProductFaker = require('@data/faker/product');

const baseContext = 'functional_BO_catalog_monitoring_monitoringProducts';

let browserContext;
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

/*
Create new product
Check existence of new product in monitoring page
Delete product and check deletion in products page
 */
describe('BO - Catalog - Monitoring : Create different products and delete them from monitoring page', async () => {
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
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_goToProductsPage`,
          baseContext,
        );

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );

        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should reset all filters and get number of products in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}_resetFirst`, baseContext);

        numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });

      it('should create product and check the products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}_create`, baseContext);

        await productsPage.goToAddProductPage(page);
        let createProductMessage = await addProductPage.createEditBasicProduct(
          page,
          test.args.productToCreate,
        );

        if (test.args.hasCombinations) {
          createProductMessage = await addProductPage.setCombinationsInProduct(
            page,
            test.args.productToCreate,
          );
        }

        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });

    describe('Check created product in monitoring page', async () => {
      it('should go to \'Catalog > Monitoring\' page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_goToMonitoringPage`,
          baseContext,
        );

        await addProductPage.goToSubMenu(
          page,
          addProductPage.catalogParentLink,
          addProductPage.monitoringLink,
        );

        const pageTitle = await monitoringPage.getPageTitle(page);
        await expect(pageTitle).to.contains(monitoringPage.pageTitle);

        numberOfProductsIngrid = await monitoringPage.resetAndGetNumberOfLines(
          page,
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

        await monitoringPage.filterTable(
          page,
          test.args.gridName,
          'input',
          'name',
          test.args.productToCreate.name,
        );

        const textColumn = await monitoringPage.getTextColumnFromTable(
          page,
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

        numberOfProductsIngrid = await monitoringPage.resetAndGetNumberOfLines(page, test.args.gridName);
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

        await monitoringPage.filterTable(
          page,
          test.args.gridName,
          'input',
          'name',
          test.args.productToCreate.name,
        );

        const textColumn = await monitoringPage.getTextColumnFromTable(
          page,
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

        const textResult = await monitoringPage.deleteProductInGrid(page, test.args.gridName, 1);
        await expect(textResult).to.equal(productsPage.productDeletedSuccessfulMessage);

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should reset filter check number of products', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_resetInProductsPage`,
          baseContext,
        );

        const numberOfProductsAfterDelete = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProductsAfterDelete).to.be.equal(numberOfProducts);
      });
    });
  });
});
