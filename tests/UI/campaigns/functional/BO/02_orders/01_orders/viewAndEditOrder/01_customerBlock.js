require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');
const customersPage = require('@pages/BO/customers');
const addressesPage = require('@pages/BO/customers/addresses');
const viewCustomerPage = require('@pages/BO/customers/view');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foCreateAccountPage = require('@pages/FO/myAccount/add');
const foMyAccountPage = require('@pages/FO/myAccount');
const foAddressesPage = require('@pages/FO/myAccount/addresses');
const foAddAddressesPage = require('@pages/FO/myAccount/addAddress');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

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

const customerData = new CustomerFaker();
const firstAddressData = new AddressFaker({firstName: 'first', country: 'France'});
const secondAddressData = new AddressFaker({firstName: 'second', country: 'France'});
const editShippingAddressData = new AddressFaker({country: 'France'});
const editInvoiceAddressData = new AddressFaker({country: 'France'});

const privateNote = 'Test private note';
let customerID = 0;
let addressID = 0;

/*
Pre-Conditions (Create customer, create 2 addresses for the customer in FO)
Create order by new customer in FO
Go to orders page BO and view the created order page
Check customer block content
- Customer’s title, name, last name, customer reference
- Email and validated orders number
- Shipping and invoice address
- Private note
Check that private note is closed by default
Check that the other customer doesn't have the private note
*/
describe('BO - Orders - view and edit order : Check and edit customer block', async () => {
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

  // Pre-Condition - create customer
  describe('Create new customer', async () => {
    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foHomePage.goToLoginPage(page);
      await foLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await foCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foHomePage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;
    });
  });

  // Pre-Condition - Create 2 addresses for the customer
  describe('Create address', async () => {
    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foHomePage.goToMyAccountPage(page);

      const pageTitle = await foMyAccountPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foMyAccountPage.pageTitle);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAddressesPage', baseContext);

      await foMyAccountPage.goToAddFirstAddressPage(page);

      const pageHeaderTitle = await foAddressesPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foAddressesPage.addressPageTitle);
    });

    it('should create the first address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await foAddAddressesPage.setAddress(page, firstAddressData);
      await expect(textResult).to.equal(foAddressesPage.addAddressSuccessfulMessage);
    });

    it('should go to create address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage2', baseContext);

      await foAddressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await foAddAddressesPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(foAddAddressesPage.creationFormTitle);
    });

    it('should create the second address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress2', baseContext);

      const textResult = await foAddAddressesPage.setAddress(page, secondAddressData);
      await expect(textResult).to.equal(foAddressesPage.addAddressSuccessfulMessage);
    });
  });

  // 1 - create order
  describe(`Create order by '${customerData.firstName} ${customerData.lastName}' in FO`, async () => {
    it('should add product to cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      // Go to home page
      await foLoginPage.goToHomePage(page);

      // Go to the first product page
      await foHomePage.goToProductPage(page, 1);

      // Add the created product to the cart
      await foProductPage.addProductToTheCart(page);

      const notificationsNumber = await foCartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      // Proceed to checkout the shopping cart
      await foCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foCheckoutPage.goToDeliveryStep(page);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

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

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.addressesLink,
      );

      const pageTitle = await addressesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addressesPage.pageTitle);
    });

    it('should get the customer address ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getCustomerAddressID', baseContext);

      await addressesPage.filterAddresses(page, 'input', 'firstname', secondAddressData.firstName);

      const numberOfAddressesAfterFilter = await addressesPage.getNumberOfElementInGrid(page);
      await expect(numberOfAddressesAfterFilter).to.be.at.most(1);

      addressID = await addressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address');
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
    });

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const customerEmail = await viewOrderPage.getCustomerEmail(page);
      await expect(customerEmail).to.contains(`mailto:${customerData.email}`);
    });

    it('should check validated orders number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidatedOrderNumber', baseContext);

      const customerEmail = await viewOrderPage.getValidatedOrdersNumber(page);
      await expect(customerEmail).to.equal(0);
    });

    it('should edit existing shipping address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editShippingAddress', baseContext);

      const shippingAddress = await viewOrderPage.editExistingShippingAddress(page, editShippingAddressData);
      await expect(shippingAddress)
        .to.contain(editShippingAddressData.firstName)
        .and.to.contain(editShippingAddressData.lastName)
        .and.to.contain(editShippingAddressData.address)
        .and.to.contain(editShippingAddressData.postalCode)
        .and.to.contain(editShippingAddressData.city)
        .and.to.contain(editShippingAddressData.country);
    });

    it('should select another shipping address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectAnotherShippingAddress', baseContext);

      const addressToSelect = `${addressID}- ${secondAddressData.address} ${secondAddressData.secondAddress} `
        + `${secondAddressData.postalCode} ${secondAddressData.city}`;

      const alertMessage = await viewOrderPage.selectAnotherShippingAddress(page, addressToSelect);
      expect(alertMessage).to.contains(viewOrderPage.successfulUpdateMessage);

      const shippingAddress = await viewOrderPage.getShippingAddress(page);
      await expect(shippingAddress)
        .to.contain(secondAddressData.firstName)
        .and.to.contain(secondAddressData.lastName)
        .and.to.contain(secondAddressData.address)
        .and.to.contain(secondAddressData.postalCode)
        .and.to.contain(secondAddressData.city)
        .and.to.contain(secondAddressData.country);
    });

    it('should edit existing invoice address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editInvoiceAddress', baseContext);

      const invoiceAddress = await viewOrderPage.editExistingInvoiceAddress(page, editInvoiceAddressData);
      await expect(invoiceAddress)
        .to.contain(editInvoiceAddressData.firstName)
        .and.to.contain(editInvoiceAddressData.lastName)
        .and.to.contain(editInvoiceAddressData.address)
        .and.to.contain(editInvoiceAddressData.postalCode)
        .and.to.contain(editInvoiceAddressData.city)
        .and.to.contain(editInvoiceAddressData.country);
    });

    it('should select another invoice address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectAnotherInvoiceAddress', baseContext);

      const addressToSelect = `${addressID}- ${secondAddressData.address} ${secondAddressData.secondAddress} `
        + `${secondAddressData.postalCode} ${secondAddressData.city}`;

      const alertMessage = await viewOrderPage.selectAnotherInvoiceAddress(page, addressToSelect);
      expect(alertMessage).to.contains(viewOrderPage.successfulUpdateMessage);

      const shippingAddress = await viewOrderPage.getInvoiceAddress(page);
      await expect(shippingAddress)
        .to.contain(secondAddressData.firstName)
        .and.to.contain(secondAddressData.lastName)
        .and.to.contain(secondAddressData.address)
        .and.to.contain(secondAddressData.postalCode)
        .and.to.contain(secondAddressData.city)
        .and.to.contain(secondAddressData.country);
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
  describe(`Delete the created customer '${customerData.firstName} ${customerData.lastName}'`, async () => {
    it('should go \'Customers > Customers\' page', async function () {
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

    it('should delete customer and check result', async function () {
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
