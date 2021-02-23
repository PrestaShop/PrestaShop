require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const customersPage = require('@pages/BO/customers');

// Import data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_productBlock';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

const customerData = new CustomerFaker({password: ''});
const addressData = new AddressFaker({country: 'France'});
const ProductFaker = require('@data/faker/product');

const productOutOfStockAllowed = new ProductFaker({
  name: 'Out of stock allowed',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -12,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Allow orders',
});
const productOutOfStockNotAllowed = new ProductFaker({
  name: 'Out of stock not allowed',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -36,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Deny orders',
});
const packOfProducts = new ProductFaker({
  name: 'Pack of products',
  type: 'Pack of products',
  pack: {demo_13: 10, demo_7: 5},
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});
const virtualProduct = new ProductFaker({
  name: 'Virtual',
  type: 'Virtual product',
  taxRule: 'No tax',
  quantity: 20,
});
const combinationProduct = new ProductFaker({
  name: 'Product with combination',
  type: 'Standard product',
  productHasCombinations: true,
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 1,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});
const simpleProduct = new ProductFaker({
  name: 'Simple product',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 50,
  minimumQuantity: 1,
  stockLocation: 'stock 1',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
});
const productQuantity = 0;
const newQuantity = 2;
const newPrice = 25;

/*
Create order by guest in FO
Create 5 products in BO: Out of stock allowed, out of stock not allowed, with combination, pack and virtual
Check product block content:
- Check Number of products
- Update the price of an ordered
- Update the quantity of an ordered product
- Add and check product out of stock not allowed
- Add and check product out of stock allowed
- Add all types of products
*/
describe('Check product block in view order page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foHomePage.goToFo(page);
    await foHomePage.changeLanguage(page, 'en');

    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;
  });

  // 1 - create order
  describe(`Create order by '${customerData.firstName} ${customerData.lastName}' in FO`, async () => {
    it('should add product to cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHomePage.goToHomePage(page);

      // Go to the fourth product page
      await foHomePage.goToProductPage(page, 4);

      // Add the created product to the cart
      await foProductPage.addProductToTheCart(page, productQuantity);

      // Proceed to checkout the shopping cart
      await foCartPage.clickOnProceedToCheckout(page);

      // Go to checkout page
      const isCheckoutPage = await foCheckoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    it('should fill guest personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setPersonalInformation', baseContext);

      const isStepPersonalInfoCompleted = await foCheckoutPage.setGuestPersonalInformation(page, customerData);
      await expect(isStepPersonalInfoCompleted, 'Step personal information is not completed').to.be.true;
    });

    it('should fill address form and go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

      const isStepAddressComplete = await foCheckoutPage.setAddress(page, addressData);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should validate the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'validateOrder', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;

      // Payment step - Choose payment step
      await foCheckoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
      const cardTitle = await foOrderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      await expect(cardTitle).to.contains(foOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('login in BO', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });
  });

  // 2 - Create 5 products
  [productOutOfStockAllowed,
    productOutOfStockNotAllowed,
    packOfProducts,
    virtualProduct,
    combinationProduct,
    simpleProduct,
  ].forEach((product, index) => {
    describe(`Create product '${product.name}'`, async () => {
      it('should go to Products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFiltersBeforeCreate${index}`, baseContext);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });

      it('should create Product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        await productsPage.goToAddProductPage(page);
        let createProductMessage = '';
        if (product === virtualProduct) {
          createProductMessage = await addProductPage.createEditBasicProduct(page, product);
        } else {
          createProductMessage = await addProductPage.setProduct(page, product);
        }
        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });
  });

  // 3 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to Orders page', async function () {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 4 - Check product block
  describe('Check product block', async () => {
    describe('Add \'Simple product\' 2 times and check the error message', async () => {
      it(`should add the product '${simpleProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderSimpleProduct1', baseContext);

        await viewOrderPage.SearchProduct(page, simpleProduct.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(simpleProduct.stockLocation),
          expect(result.available).to.equal(simpleProduct.quantity - 1),
        ]);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
      });

      it('should add the same product and check the error message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderSimpleProduct2', baseContext);

        await viewOrderPage.SearchProduct(page, simpleProduct.name);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.errorAddSameProduct);

        await viewOrderPage.cancelAddProductToCart(page);
      });
    });

    describe('Add \'Product with combination\'', async () => {
      it(`should add the product '${combinationProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderCombinationProduct', baseContext);

        await viewOrderPage.SearchProduct(page, combinationProduct.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(combinationProduct.stockLocation),
          expect(result.available).to.equal(combinationProduct.quantity - 1),
        ]);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
      });
    });

    describe('Add \'Virtual product\'', async () => {
      it(`should add the product '${virtualProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'ordervirtualProduct', baseContext);

        await viewOrderPage.SearchProduct(page, virtualProduct.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(''),
          expect(result.available).to.equal(virtualProduct.quantity - 1),
        ]);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
      });
    });

    describe('Add \'Pack of products\'', async () => {
      it(`should add the product '${packOfProducts.name}' and test minimal quantity`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderPackOfProducts', baseContext);

        await viewOrderPage.SearchProduct(page, packOfProducts.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(packOfProducts.stockLocation),
          expect(result.available).to.equal(packOfProducts.quantity - 1),
        ]);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.errorMinimumQuantityMessage);
      });

      it('should check ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPackOfProductsDetails', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page, packOfProducts.minimumQuantity);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(packOfProducts.name),
          expect(result.basePrice).to.equal(packOfProducts.price),
          expect(result.quantity).to.equal(packOfProducts.minimumQuantity),
          expect(result.available).to.equal(packOfProducts.quantity - packOfProducts.minimumQuantity),
          expect(result.total).to.equal(packOfProducts.price * packOfProducts.minimumQuantity),
        ]);
      });
    });

    describe('Add \'Customized product\'', async () => {
      it(`should add the product '${Products.demo_14.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderCustomizedProduct', baseContext);

        await viewOrderPage.SearchProduct(page, Products.demo_14.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(''),
          expect(result.available).to.be.above(0),
        ]);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
      });
    });

    describe('Add product \'Out of stock allowed\'', async () => {
      it(`should add the product '${productOutOfStockAllowed.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderProductOutOfStockAllowed', baseContext);

        await viewOrderPage.SearchProduct(page, productOutOfStockAllowed.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productOutOfStockAllowed.stockLocation),
          expect(result.available).to.equal(productOutOfStockAllowed.quantity - 1),
        ]);

        const textResult = await viewOrderPage.addProductToCart(page);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductOutOfStockAllowedDetails', baseContext);
        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(productOutOfStockAllowed.name),
          expect(result.basePrice).to.equal(productOutOfStockAllowed.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productOutOfStockAllowed.quantity - 1),
          expect(result.total).to.equal(productOutOfStockAllowed.price),
        ]);
      });
    });

    /*describe('Add product \'Out of stock not allowed\'', async () => {
      it(`should add the product '${productOutOfStockNotAllowed.name}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderProductOutOfStockNotAllowed', baseContext);

        await viewOrderPage.SearchProduct(page, productOutOfStockNotAllowed.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productOutOfStockNotAllowed.stockLocation),
          expect(result.available).to.equal(productOutOfStockNotAllowed.quantity - 1),
        ]);

        const isDisabled = await viewOrderPage.isAddButtonDisabled(page);
        await expect(isDisabled).to.be.true;

        await viewOrderPage.cancelAddProductToCart(page);
      });
    });

    describe('Update price and quantity of an ordered product', async () => {
      it('should check number of products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts3', baseContext);

        const productCount = await viewOrderPage.getProductsNumber(page);
        await expect(productCount).to.equal(7);
      });

      it('should update the quantity of the ordered product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

        const quantity = await viewOrderPage.modifyProductQuantity(page, 1, newQuantity);
        await expect(quantity, 'Quantity was not updated').to.equal(newQuantity);
      });

      it('should update the price of the ordered product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updatePrice', baseContext);

        await viewOrderPage.modifyProductPrice(page, 1, newPrice);

        const result = await viewOrderPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.basePrice, 'Base price was not updated').to.equal(25),
          expect(result.total, 'Total price was not updated').to.equal(newPrice * newQuantity),
        ]);
      });
    });*/
  });

  /*// 5 - Delete the created customer
  describe('Delete the created customer', async () => {
    it('should go customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.customersParentLink, dashboardPage.customersLink);

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.filterCustomers(page, 'input', 'email', customerData.email);

      const textResult = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textResult).to.contains(customerData.email);
    });

    it('should delete customer and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const deleteTextResult = await customersPage.deleteCustomer(page, 1);
      await expect(deleteTextResult).to.be.equal(customersPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCustomersAfterReset = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterReset).to.be.above(0);
    });
  });

  // 6 - Delete the created products
  describe('Delete the created products', async () => {
    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

      await addProductPage.goToSubMenu(page, addProductPage.catalogParentLink, addProductPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    [productOutOfStockAllowed,
      productOutOfStockNotAllowed,
      packOfProducts,
      virtualProduct,
      combinationProduct,
      simpleProduct,
    ].forEach((product, index) => {
      it(`should delete product '${product.name}' from DropDown Menu`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

        const deleteTextResult = await productsPage.deleteProduct(page, product);
        await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFiltersAfterDelete${index}`, baseContext);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });
    });
  });*/
});
