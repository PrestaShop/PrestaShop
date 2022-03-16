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
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {bulkDeleteProductsTest} = require('@commonTests/BO/catalog/createDeleteProduct');
const {enableEcoTaxTest, disableEcoTaxTest} = require('@commonTests/BO/international/enableDisableEcoTax');
const {createCurrencyTest, deleteCurrencyTest} = require('@commonTests/BO/international/createDeleteCurrency');
const {createCartRuleTest, deleteCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule');
const {deleteNonOrderedShoppingCarts} = require('@commonTests/BO/orders/shoppingCarts');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');
const {Currencies} = require('@data/demo/currencies');

// Import faker data
const ProductFaker = require('@data/faker/product');
const CartRuleFaker = require('@data/faker/cartRule');

// Test context
const baseContext = 'functional_BO_orders_orders_createOrders_selectPreviousOrders';

let browserContext;
let page;
const prefixNewProduct = 'TOTEST';
let availableStockSimpleProduct = 0;
let availableStockCombinationProduct = 0;
let availableStockVirtualProduct = 0;
let availableStockCustomizedProduct = 0;

// Data to create pack of products with minimum quantity = 2
const packOfProducts = new ProductFaker({
  name: `Pack of products ${prefixNewProduct}`,
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

// Data to create product out of stock allowed
const productOutOfStockAllowed = new ProductFaker({
  name: `Out of stock allowed ${prefixNewProduct}`,
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -12,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Allow orders',
});

// Data to create product out of stock not allowed
const productOutOfStockNotAllowed = new ProductFaker({
  name: `Out of stock not allowed ${prefixNewProduct}`,
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -15,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Deny orders',
});

// Data to create product with specific price
const productWithSpecificPrice = new ProductFaker({
  name: `Product with specific price ${prefixNewProduct}`,
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  specificPrice: {
    discount: 50,
    startingAt: 2,
    reductionType: '%',
  },
});

// Data to create product with ecotax
const productWithEcoTax = new ProductFaker({
  name: `Product with ecotax ${prefixNewProduct}`,
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  minimumQuantity: 1,
  ecoTax: 10,
});

// Data to create product with cart rule
const productWithCartRule = new ProductFaker({
  name: `Product with cart rule ${prefixNewProduct}`,
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 50,
  minimumQuantity: 1,
  stockLocation: 'stock 1',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});

// Data to create cart rule
const newCartRuleData = new CartRuleFaker(
  {
    applyDiscountTo: 'Specific product',
    product: productWithCartRule.name,
    freeShipping: true,
    discountType: 'Amount',
    discountPercent: 20,
    discountAmount: {
      value: 20,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
    freeGift: true,
    freeGiftProduct: Products.demo_13,
  },
);

// Data to add customized value for product
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
  // Pre-condition: Enable EcoTax
  enableEcoTaxTest(baseContext);

  // Pre-condition: Create currency
  createCurrencyTest(Currencies.mad, baseContext);

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

  // Pre-condition: Create 6 products
  describe('PRE-TEST: Create 6 products in BO', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    [packOfProducts,
      productOutOfStockAllowed,
      productOutOfStockNotAllowed,
      productWithSpecificPrice,
      productWithEcoTax,
      productWithCartRule,
    ].forEach((product, index) => {
      it('should go to add product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddProductPage', baseContext);

        if (index === 0) {
          await productsPage.goToAddProductPage(page);
        } else {
          await addProductPage.goToAddProductPage(page);
        }

        const pageTitle = await addProductPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it(`create product '${product.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

        let createProductMessage = '';
        if (product === productWithSpecificPrice) {
          await addProductPage.createEditBasicProduct(page, product);
          createProductMessage = await addProductPage.addSpecificPrices(page, productWithSpecificPrice.specificPrice);
        } else {
          createProductMessage = await addProductPage.setProduct(page, product);
        }
        if (product === productWithEcoTax) {
          await addProductPage.addEcoTax(page, productWithEcoTax.ecoTax);
        }
        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });
  });

  // Pre-condition: Create cart rule and apply the discount to 'productWithCartRule'
  createCartRuleTest(newCartRuleData, baseContext);

  // Pre-condition: Delete non ordered shopping carts
  deleteNonOrderedShoppingCarts(baseContext);

  // Pre-condition: Get the available stock of the product demo_11
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
  describe('Add some products to cart and check details', async () => {
    it('should search for a non-existent product and check the alert message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNonExistentProduct', baseContext);

      const alertMessage = await addOrderPage.searchProductAndGetAlert(page, 'non existent');
      await expect(alertMessage).to.equal(addOrderPage.noProductFoundText);
    });

    it('should add to cart \'Standard simple product\' and check details', async function () {
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

    it('should add to cart the same \'Standard simple product\' and check details', async function () {
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

    it('should add to cart \'Standard product with combinations\' and check details', async function () {
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

    it('should add to cart \'Virtual product\' and check details', async function () {
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

    it('should add to cart \'Pack of products and\' check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${packOfProducts.name} - €${packOfProducts.price.toFixed(2)}`;

      const alertMessage = await addOrderPage.AddProductToCartAndGetAlert(page, packOfProducts.name, productNameToSelect, 1);
      await expect(alertMessage).to.equal('You must add a minimum quantity of 2');
    });

    it('should increase the quantity of \'Pack of products\' and add it to the cart', async function () {
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

    it('should add to cart \'Customized product\' and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${Products.demo_14.name} - €${Products.demo_14.price.toFixed(2)}`;

      const alertMessage = await addOrderPage.AddProductToCartAndGetAlert(page, Products.demo_14.name, productNameToSelect, 1);
      await expect(alertMessage).to.equal('Please fill in all the required fields.');
    });

    it('should add customized text to \'Customized product\' and add it to the cart', async function () {
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

    it('should add to cart product \'Out of stock allowed\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchStandardCombinationsProduct', baseContext);

      const productNameToSelect = `${productOutOfStockAllowed.name} - €${productOutOfStockAllowed.price.toFixed(2)}`;

      await addOrderPage.addProductToCart(page, productOutOfStockAllowed, productNameToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page, 6);
      await Promise.all([
        expect(result.image).to.contains('en-default-small_default.jpg'),
        expect(result.description).to.equal(productOutOfStockAllowed.name),
        expect(result.reference).to.equal(productOutOfStockAllowed.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(productOutOfStockAllowed.quantity),
        expect(result.price).to.equal(productOutOfStockAllowed.price),
      ]);
    });

    it('should add to cart product \'Out of stock not allowed\' and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${productOutOfStockNotAllowed.name} - €${productOutOfStockNotAllowed.price.toFixed(2)}`;

      const alertMessage = await addOrderPage.AddProductToCartAndGetAlert(page, productOutOfStockNotAllowed.name, productNameToSelect, 1);
      await expect(alertMessage).to.equal('There are not enough products in stock.');
    });

    it('should add to cart product \'With specific price\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${productWithSpecificPrice.name} - €${productWithSpecificPrice.price.toFixed(2)}`;

      await addOrderPage.addProductToCart(page, productWithSpecificPrice, productNameToSelect, 2);

      const result = await addOrderPage.getProductDetailsFromTable(page, 7);
      await Promise.all([
        expect(result.image).to.contains('en-default-small_default.jpg'),
        expect(result.description).to.equal(productWithSpecificPrice.name),
        expect(result.reference).to.equal(productWithSpecificPrice.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(productWithSpecificPrice.quantity),
        expect(result.price).to.equal(productWithSpecificPrice.price),
      ]);
    });

    it('should add to cart product \'With ecotax\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${productWithEcoTax.name} - €${productWithEcoTax.price.toFixed(2)}`;

      await addOrderPage.addProductToCart(page, productWithEcoTax, productNameToSelect, 1);

      const result = await addOrderPage.getProductDetailsFromTable(page, 8);
      await Promise.all([
        expect(result.image).to.contains('en-default-small_default.jpg'),
        expect(result.description).to.equal(productWithEcoTax.name),
        expect(result.reference).to.equal(productWithEcoTax.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(productWithEcoTax.quantity),
        expect(result.price).to.equal(productWithEcoTax.price),
      ]);
    });

    it('should add to cart product \'With cart rule\' and check the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      const productNameToSelect = `${productWithCartRule.name} - €${productWithCartRule.price.toFixed(2)}`;

      await addOrderPage.addProductToCart(page, productWithCartRule, productNameToSelect, 1);

      const result = await addOrderPage.getProductDetailsFromTable(page, 9);
      await Promise.all([
        expect(result.image).to.contains('en-default-small_default.jpg'),
        expect(result.description).to.equal(productWithCartRule.name),
        expect(result.reference).to.equal(productWithCartRule.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(productWithCartRule.quantity),
        expect(result.price).to.equal(productWithCartRule.price),
      ]);
    });

    it('should check the gift product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGiftProduct', baseContext);

      const result = await addOrderPage.getProductGiftDetailsFromTable(page, 10);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_13.coverImage),
        expect(result.description).to.equal(Products.demo_13.name),
        expect(result.reference).to.equal(Products.demo_13.reference),
        expect(result.basePrice).to.equal('Gift'),
        expect(result.quantity).to.equal(1),
        expect(result.price).to.equal('Gift'),
      ]);
    });

    it('should increase the quantity of the product \'With cart rule\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

      await addOrderPage.addProductQuantity(page, 2, 9);

      const result = await addOrderPage.getProductDetailsFromTable(page, 9);
      await Promise.all([
        expect(result.image).to.contains('en-default-small_default.jpg'),
        expect(result.description).to.equal(productWithCartRule.name),
        expect(result.reference).to.equal(productWithCartRule.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(productWithCartRule.quantity),
        expect(result.price).to.equal(productWithCartRule.price * 2),
      ]);
    });

    it('should remove the product and check that the product is deleted from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

      const isProductStillExist = await addOrderPage.removeProduct(page, 9);
      await expect(isProductStillExist, 'Product still visible in the cart!').to.be.true;
    });

    it('should check that the product and check that the product is deleted from the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeProduct', baseContext);

      const isProductStillExist = await addOrderPage.removeProduct(page, 9);
      await expect(isProductStillExist, 'Product still visible in the cart!').to.be.true;
    });

    it('should select another currency and check that the price is changed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectAnotherCurrency', baseContext);

      await addOrderPage.selectAnotherCurrency(page, 'Moroccan Dirham (MAD)');

      const result = await addOrderPage.getProductDetailsFromTable(page, 8);
      await Promise.all([
        expect(result.image).to.contains('en-default-small_default.jpg'),
        expect(result.description).to.equal(productWithEcoTax.name),
        expect(result.reference).to.equal(productWithEcoTax.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.quantityMax).to.equal(productWithEcoTax.quantity),
        expect(result.price).to.not.equal(productWithEcoTax.price),
      ]);
    });

    it('should select another language and check that the language is changed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectAnotherLanguage', baseContext);

      await addOrderPage.selectAnotherLanguage(page, 'Français (French)');

      const result = await addOrderPage.getProductDetailsFromTable(page, 2);
      await expect(result.description).to.equal(`${Products.demo_1.nameFR} S - Blanc`);
    });
  });

  // Post-condition: Delete the created products
  bulkDeleteProductsTest(prefixNewProduct, baseContext);

  // Post-condition: Disable EcoTax
  disableEcoTaxTest(baseContext);

  // Post-condition: Delete currency
  deleteCurrencyTest(Currencies.mad, baseContext);

  // Post-condition: Delete cart rule
  deleteCartRuleTest(newCartRuleData.name, baseContext);
});
