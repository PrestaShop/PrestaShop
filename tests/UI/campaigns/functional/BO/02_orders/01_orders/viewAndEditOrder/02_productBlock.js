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

const productQuantity = 4;

const productOutOfStockAllowed = {
  name: 'Out of stock allowed',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -12,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Allow orders',
};

const firstProduct = new ProductFaker(productOutOfStockAllowed);

const productOutOfStockNotAllowed = {
  name: 'Out of stock not allowed',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: -36,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Deny orders',
};

const secondProduct = new ProductFaker(productOutOfStockNotAllowed);

const packOfProducts = {
  name: 'Pack of products',
  type: 'Pack of products',
  pack: {demo_13: 10, demo_7: 5},
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
};

const thirdProduct = new ProductFaker(packOfProducts);

const virtualProduct = {
  name: 'Virtual',
  type: 'Virtual product',
  taxRule: 'No tax',
  quantity: 20,
};

const fourthProduct = new ProductFaker(virtualProduct);

const combinationProduct = {
  name: 'Product with combination',
  type: 'Standard product',
  productHasCombinations: true,
  taxRule: 'No tax',
  quantity: 197,
  minimumQuantity: 3,
  stockLocation: 'stock 3',
  lowStockLevel: 3,
  behaviourOutOfStock: 'Default behavior',
};

const fifthProduct = new ProductFaker(combinationProduct);


/*
Create order by guest in FO
Go to orders page BO and view the created order page
Check product block content
*/
describe('Check customer block in view order page', async () => {
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

  // 2 - Create product out of stock allowed
  describe('Create product out of stock allowed', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should create Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await productsPage.goToAddProductPage(page);

      const createProductMessage = await addProductPage.setProduct(page, firstProduct);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  // 3 - Create product out of stock not allowed
  describe('Create product out of stock not allowed', async () => {
    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters2', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should create Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct2', baseContext);

      await productsPage.goToAddProductPage(page);

      const createProductMessage = await addProductPage.setProduct(page, secondProduct);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  // 4 - Create pack of products
  describe('Create pack of products', async () => {
    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters2', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should create Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct2', baseContext);

      await productsPage.goToAddProductPage(page);

      const createProductMessage = await addProductPage.setProduct(page, thirdProduct);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });// 4 - Create pack of products

  // 5 - Create virtual product
  describe('Create virtual product', async () => {
    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters2', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should create Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct2', baseContext);

      await productsPage.goToAddProductPage(page);

      const createProductMessage = await addProductPage.createEditBasicProduct(page, fourthProduct);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  // 6 - Create product with combination
  describe('Create product with combination', async () => {
    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters2', baseContext);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should create Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct2', baseContext);

      await productsPage.goToAddProductPage(page);

      const createProductMessage = await addProductPage.setProduct(page, fifthProduct);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  // 6 - Go to view order page
  describe('View order page', async () => {
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

  // 7 - check product block
  describe('View product block', async () => {
    it('should check number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      const productCount = await viewOrderPage.getProductsNumber(page);
      await expect(productCount).to.equal(1);
    });

    it('Update the quantity of an ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePrice', baseContext);

      const newQuantity = await viewOrderPage.modifyProductQuantity(page, 1, 2);
      await expect(newQuantity, 'Quantity was not updated').to.equal(2);
    });

    it('Update the price of an ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePrice', baseContext);

      await viewOrderPage.modifyProductPrice(page, 1, 25);

      const result = await viewOrderPage.getProductDetails(page, 1);
      await Promise.all([
        expect(result.basePrice, 'Base price was not updated').to.equal(25),
        expect(result.total, 'Total price was not updated').to.equal(25 * 2),
      ]);
    });

    it('should add the created product \'Out of stock allowed\' to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await viewOrderPage.SearchProduct(page, firstProduct.name);
      const result = await viewOrderPage.getSearchedProductDetails(page);
      await Promise.all([
        expect(result.stockLocation).to.equal(firstProduct.stockLocation),
        expect(result.available).to.equal(firstProduct.quantity - 1),
      ]);

      const textResult = await viewOrderPage.addProductToCart(page);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);

      await viewOrderPage.closeGrowlMessage(page);
    });

    it('should check product \'Out of stock allowed\' details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);
      const result = await viewOrderPage.getProductDetails(page, 1);
      await Promise.all([
        expect(result.name).to.equal(firstProduct.name),
        expect(result.basePrice).to.equal(firstProduct.price),
        expect(result.quantity).to.equal(1),
        expect(result.available).to.equal(firstProduct.quantity - 1),
        expect(result.total).to.equal(firstProduct.price),
      ]);
    });

    it('should try to add product \'Out of stock not allowed\' to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await viewOrderPage.SearchProduct(page, secondProduct.name);
      const result = await viewOrderPage.getSearchedProductDetails(page);
      await Promise.all([
        expect(result.stockLocation).to.equal(secondProduct.stockLocation),
        expect(result.available).to.equal(secondProduct.quantity - 1),
      ]);

      const isDisabled = await viewOrderPage.isAddButtonDisabled(page);
      await expect(isDisabled).to.be.true;

      await viewOrderPage.cancelAddProductToCart(page);
    });

    it('should add the created product \'Pack of products\' to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await viewOrderPage.SearchProduct(page, thirdProduct.name);
      const result = await viewOrderPage.getSearchedProductDetails(page);
      await Promise.all([
        expect(result.stockLocation).to.equal(thirdProduct.stockLocation),
        expect(result.available).to.equal(thirdProduct.quantity - 1),
      ]);

      let textResult = await viewOrderPage.addProductToCart(page);
      await expect(textResult).to.contains(viewOrderPage.errorAddProductMessage);

      await viewOrderPage.closeGrowlMessage(page);

      textResult = await viewOrderPage.addProductToCart(page, thirdProduct.minimumQuantity);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
    });

    it('should check product \'Pack of products\' details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductDetails', baseContext);

      const result = await viewOrderPage.getProductDetails(page, 1);
      await Promise.all([
        expect(result.name).to.equal(thirdProduct.name),
        expect(result.basePrice).to.equal(thirdProduct.price),
        expect(result.quantity).to.equal(thirdProduct.minimumQuantity),
        expect(result.available).to.equal(thirdProduct.quantity - thirdProduct.minimumQuantity),
        expect(result.total).to.equal(thirdProduct.price * thirdProduct.minimumQuantity),
      ]);
    });

    it('should add the created product \'Virtual\' to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await viewOrderPage.SearchProduct(page, fourthProduct.name);
      const result = await viewOrderPage.getSearchedProductDetails(page);
      await Promise.all([
        expect(result.stockLocation).to.equal(''),
        expect(result.available).to.equal(fourthProduct.quantity - 1),
      ]);

      const textResult = await viewOrderPage.addProductToCart(page);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
    });

    it('should check number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      const productCount = await viewOrderPage.getProductsNumber(page);
      await expect(productCount).to.equal(3);
    });

    it('should add the created product \'Product with combination\' to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await viewOrderPage.SearchProduct(page, fifthProduct.name);
      const result = await viewOrderPage.getSearchedProductDetails(page);
      await Promise.all([
        expect(result.stockLocation).to.equal(fifthProduct.stockLocation),
        expect(result.available).to.equal(fifthProduct.quantity - 1),
      ]);

      const textResult = await viewOrderPage.addProductToCart(page);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
    });

    it('should check number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      const productCount = await viewOrderPage.getProductsNumber(page);
      await expect(productCount).to.equal(4);
    });
  });

  // 8 - Delete the created customer
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

  // 9 - Delete the created products
  describe('Delete the created products', async () => {
    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await addProductPage.goToSubMenu(page, addProductPage.catalogParentLink, addProductPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    [firstProduct, secondProduct, thirdProduct, fourthProduct, fifthProduct].forEach((product, index) => {
      it(`should delete product '${product.name}' from DropDown Menu`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

        const deleteTextResult = await productsPage.deleteProduct(page, product);
        await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilters${index}`, baseContext);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });
    });
  });
});
