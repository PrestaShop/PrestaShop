// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

// Import BO common tests
import loginCommon from '@commonTests/BO/loginBO';

require('module-alias/register');

const {expect} = require('chai');
const {enableEcoTaxTest, disableEcoTaxTest} = require('@commonTests/BO/international/enableDisableEcoTax');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');
const {deleteCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule');
const {bulkDeleteProductsTest} = require('@commonTests/BO/catalog/createDeleteProduct');

// Import FO common tests
const {createOrderByGuestTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const cartRulesPage = require('@pages/BO/catalog/discounts');
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');
const ProductFaker = require('@data/faker/product');
const CartRuleFaker = require('@data/faker/cartRule');

// Import demo data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Products} = require('@data/demo/products');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_productBlock';

let browserContext;
let page;
// Prefix for the new products to simply delete them by bulk actions
const prefixNewProduct = 'TOTEST';

const customerData = new CustomerFaker({password: ''});
const addressData = new AddressFaker({country: 'France'});

// New order by guest data
const orderData = {
  customer: customerData,
  product: 4,
  productQuantity: 1,
  address: addressData,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};
const productOutOfStockAllowed = new ProductFaker({
  name: `Out of stock allowed ${prefixNewProduct}`,
  reference: 'd12345',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -12,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Allow orders',
});
const productOutOfStockNotAllowed = new ProductFaker({
  name: `Out of stock not allowed ${prefixNewProduct}`,
  reference: 'e12345',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -36,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Deny orders',
});
const packOfProducts = new ProductFaker({
  name: `Pack of products ${prefixNewProduct}`,
  reference: 'c12345',
  type: 'Pack of products',
  pack: {demo_13: 1, demo_7: 1},
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});
const virtualProduct = new ProductFaker({
  name: `Virtual product ${prefixNewProduct}`,
  reference: 'b12345',
  type: 'Virtual product',
  taxRule: 'No tax',
  quantity: 20,
});
const combinationProduct = new ProductFaker({
  name: `Product with combination ${prefixNewProduct}`,
  reference: 'a12345',
  type: 'Standard product',
  productHasCombinations: true,
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});
const simpleProduct = new ProductFaker({
  name: `Simple product ${prefixNewProduct}`,
  reference: 'i12345',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 50,
  price: 20,
  minimumQuantity: 1,
  stockLocation: 'stock 1',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});

const productWithSpecificPrice = new ProductFaker({
  name: `Product with specific price ${prefixNewProduct}`,
  reference: 'f12345',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  specificPrice: {
    discount: 50,
    startingAt: 2,
    reductionType: '%',
  },
});

const productWithEcoTax = new ProductFaker({
  name: `Product with ecotax ${prefixNewProduct}`,
  reference: 'g12345',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  minimumQuantity: 1,
  ecoTax: 10,
});

const productWithCartRule = new ProductFaker({
  name: `Product with cart rule ${prefixNewProduct}`,
  reference: 'h12345',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 50,
  minimumQuantity: 1,
  stockLocation: 'stock 1',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});

const newCartRuleData = new CartRuleFaker(
  {
    code: '4QABV6L3',
    discountType: 'Percent',
    discountPercent: 20,
    applyDiscountTo: 'Specific product',
    product: productWithCartRule.name,
  },
);

const newQuantity = 2;
const newPrice = 25;
let productNumber = 0;

/*
Pre-condition:
- Create order by guest in FO
- Enable eco tax
- Create 9 products in BO:
  - Out of stock allowed
  - Out of stock not allowed
  - Simple
  - Standard with combination
  - Pack
  - Virtual
  - With specific price
  - With ecoTax
  - With cart rule
- Create cart rule for the product with cart rule
Scenario:
Check product block content:
  - Check number of products
  - Update the price of an ordered
  - Update the quantity of an ordered product
  - Add and check product out of stock not allowed
  - Add and check product out of stock allowed
  - Add all types of products
  - Pagination next and previous
  - Delete product
Post-condition:
- Delete guest customer
- Disable ecoTax
- Delete created products
- Delete cart rule
*/
describe('BO - Orders - View and edit order : Check product block in view order page', async () => {
  // Pre-condition: Create order by guest
  createOrderByGuestTest(orderData, `${baseContext}_preTest_1`);

  // Pre-condition: Enable EcoTax
  enableEcoTaxTest(`${baseContext}_preTest_2`);

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

  // Pre-condition: Create 9 products, Enable ecoTax, Create cart rule
  describe('PRE-TEST: Create 9 products in BO', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersBeforeCreate', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    [productOutOfStockAllowed,
      productOutOfStockNotAllowed,
      packOfProducts,
      virtualProduct,
      combinationProduct,
      simpleProduct,
      productWithSpecificPrice,
      productWithEcoTax,
      productWithCartRule,
    ].forEach((product, index) => {
      describe(`Create product : '${product.name}'`, async () => {
        it('should go to add product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAddProductPage${index}`, baseContext);

          if (index === 0) {
            await productsPage.goToAddProductPage(page);
          } else {
            await addProductPage.goToAddProductPage(page);
          }

          const pageTitle = await addProductPage.getPageTitle(page);
          await expect(pageTitle).to.contains(addProductPage.pageTitle);
        });

        it('should create Product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

          let createProductMessage = '';

          if (product === virtualProduct || product === productWithSpecificPrice) {
            createProductMessage = await addProductPage.createEditBasicProduct(page, product);
          } else {
            createProductMessage = await addProductPage.setProduct(page, product);
          }
          if (product === productWithSpecificPrice) {
            await addProductPage.addSpecificPrices(page, productWithSpecificPrice.specificPrice);
          }
          if (product === productWithEcoTax) {
            await addProductPage.addEcoTax(page, productWithEcoTax.ecoTax);
          }
          await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
        });
      });
    });

    describe(`Create cart rule and apply the discount to : '${productWithCartRule.name}'`, async () => {
      it('should go to \'Catalog > Discounts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.discountsLink,
        );

        const pageTitle = await cartRulesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await cartRulesPage.goToAddNewCartRulesPage(page);
        const pageTitle = await addCartRulePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCartRulePage.pageTitle);
      });

      it('should create new cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await addCartRulePage.createEditCartRules(page, newCartRuleData);
        await expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
      });
    });
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
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

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageProductsBlock.pageTitle);
    });
  });

  // 2 - Check product block
  describe('Check product block', async () => {
    it('should delete the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textResult = await orderPageProductsBlock.deleteProduct(page, 1);
      await expect(textResult).to.contains(orderPageProductsBlock.successfulDeleteProductMessage);
    });

    it('should check number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts0', baseContext);

      const productCount = await orderPageProductsBlock.getProductsNumber(page);
      await expect(productCount).to.equal(productNumber);
    });

    describe('Add \'Simple product\' 2 times and check the error message', async () => {
      it(`should search for the product '${simpleProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchSimpleProduct1', baseContext);

        await orderPageProductsBlock.searchProduct(page, simpleProduct.name);

        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(simpleProduct.stockLocation),
          expect(result.available).to.equal(simpleProduct.quantity - 1),
          expect(result.price).to.equal(simpleProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addSimpleProductToTheCart1', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSimpleProductDetails', baseContext);

        const result = await orderPageProductsBlock.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(simpleProduct.name),
          expect(result.reference).to.equal(`Reference number: ${simpleProduct.reference}`),
          expect(result.basePrice).to.equal(simpleProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(simpleProduct.quantity - 1),
          expect(result.total).to.equal(simpleProduct.price),
        ]);
      });

      it('should add the same product and check the error message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderSimpleProduct2', baseContext);

        await orderPageProductsBlock.searchProduct(page, simpleProduct.name);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.errorAddSameProduct);
      });

      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOncancelButton1', baseContext);

        await orderPageProductsBlock.cancelAddProductToCart(page);

        const isVisible = await orderPageProductsBlock.isAddProductTableRowVisible(page);
        await expect(isVisible).to.be.false;
      });
    });

    describe('Add \'Standard product with combination\'', async () => {
      it(`should search for the product '${combinationProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchCombinationProduct', baseContext);

        await orderPageProductsBlock.searchProduct(page, combinationProduct.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.equal(combinationProduct.quantity - 1),
          expect(result.price).to.equal(combinationProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addCombinationProduct', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCombinationProductDetails', baseContext);

        const result = await orderPageProductsBlock.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(`${combinationProduct.name} (Size: `
            + `${combinationProduct.attributes.size[0]} - Color: ${combinationProduct.attributes.color[0]})`),
          expect(result.reference).to.equal(`Reference number: ${combinationProduct.reference}`),
          expect(result.basePrice).to.equal(combinationProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(combinationProduct.quantity - 1),
          expect(result.total).to.equal(combinationProduct.price),
        ]);
      });
    });

    describe('Add \'Virtual product\'', async () => {
      it(`should search for the product '${virtualProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

        await orderPageProductsBlock.searchProduct(page, virtualProduct.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.equal(virtualProduct.quantity - 1),
          expect(result.price).to.equal(virtualProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addVirtualProductToTheCart', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkVirtualProductDetails', baseContext);

        const result = await orderPageProductsBlock.getProductDetails(page, 2);
        await Promise.all([
          expect(result.name).to.equal(virtualProduct.name),
          expect(result.reference).to.equal(`Reference number: ${virtualProduct.reference}`),
          expect(result.basePrice).to.equal(virtualProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(virtualProduct.quantity - 1),
          expect(result.total).to.equal(virtualProduct.price),
        ]);
      });
    });

    describe('Add \'Pack of products\'', async () => {
      it(`should search for the product '${packOfProducts.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchPackOfProducts', baseContext);

        await orderPageProductsBlock.searchProduct(page, packOfProducts.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(packOfProducts.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(packOfProducts.price),
        ]);
      });

      it('should try to add the product to the cart and check the error message for the minimal '
        + 'quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPackOfProductsToTheCart', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.errorMinimumQuantityMessage);
      });

      it('should increase the quantity and check ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPackOfProductsDetails', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page, packOfProducts.minimumQuantity);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);

        const result = await orderPageProductsBlock.getProductDetails(page, 3);
        await Promise.all([
          expect(result.name).to.equal(packOfProducts.name),
          expect(result.reference).to.equal(`Reference number: ${packOfProducts.reference}`),
          expect(result.basePrice).to.equal(packOfProducts.price),
          expect(result.quantity).to.equal(packOfProducts.minimumQuantity),
          expect(result.available).to.equal(packOfProducts.quantity - packOfProducts.minimumQuantity),
          expect(result.total).to.equal(packOfProducts.price * packOfProducts.minimumQuantity),
        ]);
        productNumber += 1;
      });
    });

    describe('Add \'Customized product\'', async () => {
      it(`should search for the product '${Products.demo_14.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchCustomizedProduct', baseContext);

        await orderPageProductsBlock.searchProduct(page, Products.demo_14.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(Products.demo_14.priceTaxIncl),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addCustomizedProductToTheCart', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomizedProductDetails', baseContext);

        const result = await orderPageProductsBlock.getProductDetails(page, 4);
        await Promise.all([
          expect(result.name).to.equal(Products.demo_14.name),
          expect(result.reference).to.equal(`Reference number: ${Products.demo_14.reference}`),
          expect(result.basePrice).to.equal(Products.demo_14.priceTaxIncl),
          expect(result.quantity).to.equal(1),
          expect(result.total).to.equal(Products.demo_14.priceTaxIncl),
        ]);
      });
    });

    describe('Add product \'Out of stock allowed\'', async () => {
      it(`should search for the product '${productOutOfStockAllowed.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductOutOfStockAllowed', baseContext);

        await orderPageProductsBlock.searchProduct(page, productOutOfStockAllowed.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productOutOfStockAllowed.stockLocation),
          expect(result.available).to.equal(productOutOfStockAllowed.quantity - 1),
          expect(result.price).to.equal(productOutOfStockAllowed.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductOutOfStockAllowed', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductOutOfStockAllowedDetails', baseContext);

        const result = await orderPageProductsBlock.getProductDetails(page, 4);
        await Promise.all([
          expect(result.name).to.equal(productOutOfStockAllowed.name),
          expect(result.basePrice).to.equal(productOutOfStockAllowed.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productOutOfStockAllowed.quantity - 1),
          expect(result.total).to.equal(productOutOfStockAllowed.price),
        ]);
      });

      it('should update quantity of the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityOutOfStockAllowed', baseContext);

        const quantity = await orderPageProductsBlock.modifyProductQuantity(page, 1, newQuantity);
        await expect(quantity, 'Quantity was not updated').to.equal(newQuantity);
      });
    });

    describe('Add product \'Out of stock not allowed\'', async () => {
      it(`should search for the product '${productOutOfStockNotAllowed.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductOutOfStockNotAllowed', baseContext);

        await orderPageProductsBlock.searchProduct(page, productOutOfStockNotAllowed.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productOutOfStockNotAllowed.stockLocation),
          expect(result.available).to.equal(productOutOfStockNotAllowed.quantity - 1),
          expect(result.price).to.equal(productOutOfStockNotAllowed.price),
        ]);
      });

      it('should check that add button is disabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatAddButtonIsDisabled', baseContext);

        const isDisabled = await orderPageProductsBlock.isAddButtonDisabled(page);
        await expect(isDisabled).to.be.true;
      });

      it('should increase the quantity of the product and check that cancel button still disabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityOutOfStockNotAllowed', baseContext);

        await orderPageProductsBlock.addQuantity(page, newQuantity);

        const isDisabled = await orderPageProductsBlock.isAddButtonDisabled(page);
        await expect(isDisabled).to.be.true;
      });

      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnCancelButton2', baseContext);

        await orderPageProductsBlock.cancelAddProductToCart(page);

        const isVisible = await orderPageProductsBlock.isAddProductTableRowVisible(page);
        await expect(isVisible).to.be.false;
      });
    });

    describe('Add \'Product with specific price\'', async () => {
      it(`should search for the product '${productWithSpecificPrice.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithSpecificPrice', baseContext);

        await orderPageProductsBlock.searchProduct(page, productWithSpecificPrice.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithSpecificPrice.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithSpecificPriceToTheCart', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithSpecificPriceDetails', baseContext);

        const result = await orderPageProductsBlock.getProductDetails(page, 6);
        await Promise.all([
          expect(result.name).to.equal(productWithSpecificPrice.name),
          expect(result.basePrice).to.equal(productWithSpecificPrice.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithSpecificPrice.quantity - 1),
          expect(result.total).to.equal(productWithSpecificPrice.price),
        ]);
      });
    });

    describe('Add \'Product with eco tax\'', async () => {
      it(`should search for the product '${productWithEcoTax.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithEcoTax', baseContext);

        await orderPageProductsBlock.searchProduct(page, productWithEcoTax.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productWithEcoTax.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithEcoTax.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithEcoTax', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithEcoTaxDetails', baseContext);

        const result = await orderPageProductsBlock.getProductDetails(page, 7);
        await Promise.all([
          expect(result.name).to.equal(productWithEcoTax.name),
          expect(result.basePrice).to.equal(productWithEcoTax.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithEcoTax.quantity - 1),
          expect(result.total).to.equal(productWithEcoTax.price),
        ]);
      });
    });

    describe('Add \'Product with cart rule\'', async () => {
      it(`should search for the product '${productWithCartRule.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithCartRule', baseContext);

        await orderPageProductsBlock.searchProduct(page, productWithCartRule.name);
        const result = await orderPageProductsBlock.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productWithCartRule.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithCartRule.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithCartRuleToTheCart', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithCartRuleDetails', baseContext);

        const result = await orderPageProductsBlock.getProductDetails(page, 8);
        await Promise.all([
          expect(result.name).to.equal(productWithCartRule.name),
          expect(result.basePrice).to.equal(productWithCartRule.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithCartRule.quantity - 1),
          expect(result.total).to.equal(productWithCartRule.price),
        ]);
      });
    });

    describe('Update price and quantity of the first ordered product in the list', async () => {
      it('should update the quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

        const quantity = await orderPageProductsBlock.modifyProductQuantity(page, 1, newQuantity);
        await expect(quantity, 'Quantity was not updated').to.equal(newQuantity);
      });

      it('should update the price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updatePrice', baseContext);

        await orderPageProductsBlock.modifyProductPrice(page, 1, newPrice);

        const result = await orderPageProductsBlock.getProductDetails(page, 1);
        await Promise.all([
          expect(result.basePrice, 'Base price was not updated').to.equal(25),
          expect(result.total, 'Total price was not updated').to.equal(newPrice * newQuantity),
        ]);
      });
    });

    describe('Check items per page', async () => {
      it('should check number of products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

        const productCount = await orderPageProductsBlock.getProductsNumber(page);
        await expect(productCount).to.equal(productNumber);
      });

      describe('Paginate between pages', async () => {
        it('should display 8 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayDefaultItemsNumber', baseContext);

          const isNextLinkVisible = await orderPageProductsBlock.selectPaginationLimit(page, '8');
          await expect(isNextLinkVisible).to.be.true;
        });

        it('should click on next', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

          const paginationNumber = await orderPageProductsBlock.paginationNext(page);
          await expect(paginationNumber).to.equal('2');
        });

        it('should click on previous', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

          const paginationNumber = await orderPageProductsBlock.paginationPrevious(page);
          await expect(paginationNumber).to.equal('1');
        });

        it('should display 20 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayAllItems', baseContext);

          const isNextLinkVisible = await orderPageProductsBlock.selectPaginationLimit(page, '20');
          await expect(isNextLinkVisible).to.be.false;
        });

        it('should display 8 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'redisplayDefaultItemsNumber', baseContext);

          const isNextLinkVisible = await orderPageProductsBlock.selectPaginationLimit(page, '8');
          await expect(isNextLinkVisible).to.be.true;
        });
      });
    });
  });

  // Post-condition: Delete the created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-condition: Delete the created products
  bulkDeleteProductsTest(prefixNewProduct, `${baseContext}_postTest_2`);

  // Post-condition: Delete cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_postTest_3`);

  // Post-condition: Disable EcoTax
  disableEcoTaxTest(`${baseContext}_postTest_4`);
});
