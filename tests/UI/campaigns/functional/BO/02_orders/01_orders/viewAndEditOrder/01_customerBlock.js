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
const customersPage = require('@pages/BO/customers');
const viewCustomerPage = require('@pages/BO/customers/view');

// Import data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_customerBlock';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

const customerData = new CustomerFaker({password: ''});
const addressData = new AddressFaker({country: 'France'});
const privateNote = 'Test private note';
let customerID = 0;

/*
Create order by guest in FO
Go to orders page BO and view the created order page
Check customer block content
- Customer’s title, name, last name, customer reference
- Email and validated orders number
- Shipping and invoice address
- Private note
Check that private note is closed by default
Check that the other customer doesn't have the private note
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
      await foProductPage.addProductToTheCart(page);

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

  // 2 - Go to view order page
  describe('View order page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

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

  // 3 - check customer block
  describe('View customer block', async () => {
    it('should click on \'View full details\' and check customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewFullDetails', baseContext);

      await viewOrderPage.goToViewFullDetails(page);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerData.lastName);
    });

    it('should get the customer ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getCustomerID', baseContext);

      customerID = await viewCustomerPage.getCustomerID(page);
      await expect(customerID).to.be.above(0);
    });

    it('should go back to Orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage2', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should check customer title, name, lastname, reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInfo', baseContext);

      const customerInfo = await viewOrderPage.getCustomerInfoBlock(page);
      await expect(customerInfo).to.contains(customerData.socialTitle);
      await expect(customerInfo).to.contains(customerData.firstName);
      await expect(customerInfo).to.contains(customerData.lastName);
      await expect(customerInfo).to.contains(customerID.toString());
      await expect(customerInfo).to.contains('Guest');
    });

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const customerEmail = await viewOrderPage.getCustomerEmail(page);
      await expect(customerEmail).to.contains(customerData.email);
    });

    it('should check validated orders number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidatedOrderNumber', baseContext);

      const customerEmail = await viewOrderPage.getValidatedOrdersNumber(page);
      await expect(customerEmail).to.equal(0);
    });

    it('should check order shipping', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

      const shippingAddress = await viewOrderPage.getShippingAddress(page);
      await expect(shippingAddress)
        .to.contain(customerData.firstName)
        .and.to.contain(customerData.lastName)
        .and.to.contain(addressData.address)
        .and.to.contain(addressData.postalCode)
        .and.to.contain(addressData.city)
        .and.to.contain(addressData.country);
    });

    it('should check order invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddress', baseContext);

      const invoiceAddress = await viewOrderPage.getInvoiceAddress(page);
      await expect(invoiceAddress)
        .to.contain(customerData.firstName)
        .and.to.contain(customerData.lastName)
        .and.to.contain(addressData.address)
        .and.to.contain(addressData.postalCode)
        .and.to.contain(addressData.city)
        .and.to.contain(addressData.country);
    });

    it('should check that private note textarea is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteVisible', baseContext);

      const result = await viewOrderPage.isPrivateNoteTextareaVisible(page);
      await expect(result).to.be.false;
    });

    it('should click on add new note and check that the textarea is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextarea', baseContext);

      await viewOrderPage.clickAddNewPrivateNote(page);

      const result = await viewOrderPage.isPrivateNoteTextareaVisible(page);
      await expect(result).to.be.true;
    });

    it('should go back to Orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable2', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the 1st order for the same customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage3', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should add private note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPrivateNote', baseContext);

      await viewOrderPage.clickAddNewPrivateNote(page);

      const result = await viewOrderPage.setPrivateNote(page, privateNote);
      await expect(result).to.contains(viewOrderPage.successfulUpdateMessage);
    });

    it('should go back to Orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage4', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable3', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage5', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should click on add new note and check that the textarea is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextareaVisible', baseContext);

      await viewOrderPage.clickAddNewPrivateNote(page);

      const result = await viewOrderPage.isPrivateNoteTextareaVisible(page);
      await expect(result).to.be.true;
    });

    it('should check that the private note doesn\'t exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextNotVisible', baseContext);

      const note = await viewOrderPage.getPrivateNoteContent(page);
      await expect(note).to.not.equal(privateNote);
    });

    it('should go back to Orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage3', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable4', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the 2nd order for the customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage4', baseContext);

      await ordersPage.goToOrder(page, 2);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should check that the private note is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextVisible', baseContext);

      const result = await viewOrderPage.isPrivateNoteTextareaVisible(page);
      await expect(result).to.be.true;

      const note = await viewOrderPage.getPrivateNoteContent(page);
      await expect(note).to.equal(privateNote);
    });

    it('should delete private note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePrivateNote', baseContext);

      const result = await viewOrderPage.setPrivateNote(page, '');
      await expect(result).to.contains(viewOrderPage.successfulUpdateMessage);
    });
  });

  // 4 - Delete the created customer
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
});
