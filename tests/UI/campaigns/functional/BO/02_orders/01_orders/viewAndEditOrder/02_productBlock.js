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
  quantity: -12,
  minimumQuantity: 1,
  lowStockLevel: 3,
  behaviourOutOfStock: 'Allow orders',
};

const firstProduct = new ProductFaker(productOutOfStockAllowed);

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

      await productsPage.resetFilterCategory(page);
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

  // 3 - Go to view order page
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

  // 4 - check product block
  describe('View product block', async () => {
    it('should check number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      const productCount = await viewOrderPage.getProductsNumber(page);
      await expect(productCount).to.equal(1);
    });

    it('should add the created product \'Out of stock allowed to the cart\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'AddProductToCart', baseContext);

      await viewOrderPage.SearchProduct(page, firstProduct.name);
      const result = await viewOrderPage.getSearchedProductDetail(page);
      await Promise.all([
        expect(result.stockLocation).to.equal(firstProduct.stockLocation),
        expect(result.available).to.equal(firstProduct.quantity - 1),
      ]);

      const textResult = await viewOrderPage.addProductToCart(page);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
    });
  });

  // 5 - Delete the created customer
  describe(`Delete the customer ${customerData.lastName}`, async () => {
    it('should go customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

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

  // 6 - Delete the created product
  describe('Delete the created product', async () => {
    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

      await addProductPage.goToSubMenu(page, addProductPage.catalogParentLink, addProductPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should delete product with from DropDown Menu', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteTextResult = await productsPage.deleteProduct(page, firstProduct);
      await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters2', baseContext);

      await productsPage.resetFilterCategory(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });
  });
});
