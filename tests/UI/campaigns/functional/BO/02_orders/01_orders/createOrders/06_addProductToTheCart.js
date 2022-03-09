require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const basicHelper = require('@utils/basicHelper');
const testContext = require('@utils/testContext');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const stocksPage = require('@pages/BO/catalog/stocks');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {deleteProductTest} = require('@commonTests/BO/catalog/createDeleteProduct');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');

// Import faker data
const ProductFaker = require('@data/faker/product');

// Test context
const baseContext = 'functional_BO_orders_orders_createOrders_selectPreviousOrders';

let browserContext;
let page;
let availableStockSimpleProduct = 0;
let availableStockCombinationProduct = 0;
let availableStockVirtualProduct = 0;
let availableStockCustomizedProduct = 0;
let numberOfNonOrderedShoppingCarts = 0;
let numberOfShoppingCarts = 0;

// Data to create pack of products with minimum quantity = 2
const packOfProducts = new ProductFaker({
  name: 'Pack of products',
  type: 'Pack of products',
  pack: {demo_13: 1, demo_7: 1},
  price: 12.65,
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 2,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});

const customizedProduct = {
  name: Products.demo_14.name,
  reference: Products.demo_14.reference,
  customizedValue: 'Test',
  price: Products.demo_14.price,
  thumbnailImage: Products.demo_14.thumbnailImage,
};

/*
Pre-condition:

Scenario:
- Go to create order page and choose default customer

Post-condition:

 */
describe('BO - Orders - Create order : Add a product to the cart', async () => {
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Create pack of products with minimum quantity = 2
  describe('PRE-TEST: Create pack of products', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to add product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddProductPage', baseContext);

      await productsPage.goToAddProductPage(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it('should create Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      let createProductMessage = '';

      createProductMessage = await addProductPage.setProduct(page, packOfProducts);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  // Pre-condition: Delete non ordered shopping carts
  describe('PRE-TEST: Delete the non-ordered shopping carts', async () => {
    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.shoppingCartsLink,
      );

      const pageTitle = await shoppingCartsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
    });

    it('should reset all filters and get number of shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst1', baseContext);

      numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCarts).to.be.above(0);
    });

    it('should search the non ordered shopping carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts1', baseContext);

      await shoppingCartsPage.filterTable(page, 'input', 'status', 'Non ordered');

      numberOfNonOrderedShoppingCarts = await shoppingCartsPage.getNumberOfElementInGrid(page);
      await expect(numberOfNonOrderedShoppingCarts).to.be.at.most(numberOfShoppingCarts);

      numberOfShoppingCarts -= numberOfNonOrderedShoppingCarts;

      for (let row = 1; row <= numberOfNonOrderedShoppingCarts; row++) {
        const textColumn = await shoppingCartsPage.getTextColumn(page, row, 'c!lastname');
        await expect(textColumn).to.contains('Non ordered');
      }
    });

    it('should delete the non ordered shopping carts if exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNonOrderedShoppingCartsIfExists1', baseContext);

      if (numberOfNonOrderedShoppingCarts > 0) {
        const deleteTextResult = await shoppingCartsPage.bulkDeleteShoppingCarts(page);
        await expect(deleteTextResult).to.be.contains(shoppingCartsPage.successfulMultiDeleteMessage);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteNonOrderedCarts1', baseContext);

      const numberOfShoppingCartsAfterReset = await shoppingCartsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfShoppingCartsAfterReset).to.be.equal(numberOfShoppingCarts);
    });
  });

  // Pre-condition: Get the Available stock of the product demo_11
  describe('PRE-TEST: Get the available stock of the ordered product', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.stocksLink,
      );

      const pageTitle = await stocksPage.getPageTitle(page);
      await expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it('should get the Available stock of the simple product \'demo_11\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getAvailableStockOfOrderedProduct', baseContext);

      await stocksPage.simpleFilter(page, Products.demo_11.name);

      availableStockSimpleProduct = await stocksPage.getTextColumnFromTableStocks(page, 1, 'available');
      await expect(availableStockSimpleProduct).to.be.above(0);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
      await expect(numberOfProductsAfterReset).to.be.above(1);
    });

    it('should get the Available stock of the product with combinations \'demo_1\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getAvailableStockOfOrderedProduct', baseContext);

      await stocksPage.simpleFilter(page, Products.demo_1.name);

      availableStockCombinationProduct = await stocksPage.getTextColumnFromTableStocks(page, 1, 'available');
      await expect(availableStockCombinationProduct).to.be.above(0);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
      await expect(numberOfProductsAfterReset).to.be.above(1);
    });

    it('should get the Available stock of the virtual product \'demo_18\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getAvailableStockOfOrderedProduct', baseContext);

      await stocksPage.simpleFilter(page, Products.demo_18.name);

      availableStockVirtualProduct = await stocksPage.getTextColumnFromTableStocks(page, 1, 'available');
      await expect(availableStockVirtualProduct).to.be.above(0);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
      await expect(numberOfProductsAfterReset).to.be.above(1);
    });

    it('should get the Available stock of the customized product \'demo_14\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getAvailableStockOfOrderedProduct', baseContext);

      await stocksPage.simpleFilter(page, Products.demo_14.name);

      availableStockCustomizedProduct = await stocksPage.getTextColumnFromTableStocks(page, 1, 'available');
      await expect(availableStockCustomizedProduct).to.be.above(0);
    });
  });

  // 1 - Go to create order page
  describe('Go to create order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);
      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, DefaultCustomer.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      await expect(isCartsTableVisible, 'History block is not visible!').to.be.true;
    });
  });

  // 2 - Add product to the cart
  describe('Go to create order page', async () => {
    it('should search for a non-existent product and check the alert message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonExistentProduct', baseContext);

      const alertMessage = await addOrderPage.searchProductAndGetAlert(page, 'test');
      await expect(alertMessage).to.equal(addOrderPage.noProductFoundText);
    });

    it('should add to cart standard simple product and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchStandardSimpleProduct', baseContext);

      const productNameToSelect = `${Products.demo_11.name} - €${Products.demo_11.price.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_11, productNameToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_11.thumbnailImage),
        expect(result.description).to.equal(Products.demo_11.name),
        expect(result.reference).to.equal(Products.demo_11.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(parseInt(availableStockSimpleProduct, 10)),
        expect(result.price).to.equal(Products.demo_11.price),
      ]);
    });

    it('should add to cart the same standard simple product and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchStandardSimpleProduct2', baseContext);

      const productNameToSelect = `${Products.demo_11.name} - €${Products.demo_11.price.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_11, productNameToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_11.thumbnailImage),
        expect(result.description).to.equal(Products.demo_11.name),
        expect(result.reference).to.equal(Products.demo_11.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(parseInt(availableStockSimpleProduct, 10)),
        expect(result.price).to.equal(Products.demo_11.price * 2),
      ]);
    });

    it('should add to cart standard product with combinations and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchStandardCombinationsProduct', baseContext);

      await addOrderPage.addProductToCart(page, Products.demo_1, Products.demo_1.name);
      const discountValue = await basicHelper.percentage(Products.demo_1.price, Products.demo_1.discountPercentage);

      const result = await addOrderPage.getProductDetailsFromTable(page, 2);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_1.coverImage),
        expect(result.description).to.equal(`${Products.demo_1.name} S - White`),
        expect(result.reference).to.equal(Products.demo_1.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(parseInt(availableStockCombinationProduct, 10)),
        expect(result.price).to.equal(parseFloat((Products.demo_1.price - discountValue).toFixed(2))),
      ]);
    });

    it('should add to cart virtual product and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${Products.demo_18.name} - €${Products.demo_18.price.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_18, productNameToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page, 3);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_18.thumbnailImage),
        expect(result.description).to.equal(Products.demo_18.name),
        expect(result.reference).to.equal(Products.demo_18.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(parseInt(availableStockVirtualProduct, 10)),
        expect(result.price).to.equal(Products.demo_18.price),
      ]);
    });

    it('should add to cart pack of products and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${packOfProducts.name} - €${packOfProducts.price.toFixed(2)}`;

      const alertMessage = await addOrderPage.AddProductToCartAndGetAlert(page, packOfProducts.name, productNameToSelect, 1);
      await expect(alertMessage).to.equal('You must add a minimum quantity of 2');
    });

    it('should increase the quantity of pack of products and add it to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${packOfProducts.name} - €${packOfProducts.price.toFixed(2)}`;

      await addOrderPage.addProductToCart(page, packOfProducts, productNameToSelect, 2);

      const result = await addOrderPage.getProductDetailsFromTable(page, 4);
      await Promise.all([
        expect(result.image).to.contains('en-default-small_default.jpg'),
        expect(result.description).to.equal(packOfProducts.name),
        expect(result.reference).to.equal(packOfProducts.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(packOfProducts.quantity),
        expect(result.price).to.equal(packOfProducts.price * 2),
      ]);
    });

    it('should add to cart customized product and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${Products.demo_14.name} - €${Products.demo_14.price.toFixed(2)}`;

      const alertMessage = await addOrderPage.AddProductToCartAndGetAlert(page, Products.demo_14.name, productNameToSelect, 1);
      await expect(alertMessage).to.equal('Please fill in all the required fields.');
    });

    it('should add customized text for customized Product and add it to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${customizedProduct.name} - €${customizedProduct.price.toFixed(2)}`;

      await addOrderPage.addProductToCart(page, customizedProduct, productNameToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page, 5);
      await Promise.all([
        expect(result.image).to.contains(customizedProduct.thumbnailImage),
        expect(result.description).to.equal(`${customizedProduct.name} Type your text here : ${customizedProduct.customizedValue}`),
        expect(result.reference).to.equal(customizedProduct.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(availableStockCustomizedProduct),
        expect(result.price).to.equal(customizedProduct.price),
      ]);
    });
  });

  // Post-condition: Delete the created product
  deleteProductTest(packOfProducts, baseContext);
});
