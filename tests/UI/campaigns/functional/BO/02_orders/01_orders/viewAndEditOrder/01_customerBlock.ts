// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';
import {createAccountTest, createAddressTest} from '@commonTests/FO/account';
import {createOrderByCustomerTest} from '@commonTests/FO/order';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';

// Import data
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import AddressData from '@data/faker/address';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

// Import BO pages
import customersPage from '@pages/BO/customers';
import addressesPage from '@pages/BO/customers/addresses';
import viewCustomerPage from '@pages/BO/customers/view';

// Import data
import Products from '@data/demo/products';
import CustomerData from '@data/faker/customer';
import OrderData from '@data/faker/order';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_customerBlock';

/*
Pre-Conditions:
- Create customer
- Create 2 addresses for the customer in FO
- Create order by new customer in FO
Scenario:
- Go to orders page BO and view the created order page
- Check customer block content
  - Customer’s title, name, last name, customer reference
  - Email and validated orders number
  - Shipping and invoice address
  - Private note
  - Check that private note is closed by default
  - Check that the other customer doesn't have the private note
Post-condition
- Delete the created customer
*/
describe('BO - Orders - View and edit order : Check and edit customer block', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let customerID: number = 0;
  let addressID: string = '0';

  const customerData: CustomerData = new CustomerData();
  const firstAddressData: AddressData = new AddressData({firstName: 'first', country: 'France'});
  const secondAddressData: AddressData = new AddressData({firstName: 'second', country: 'France'});
  const editShippingAddressData: AddressData = new AddressData({country: 'France'});
  const editInvoiceAddressData: AddressData = new AddressData({country: 'France'});
  const privateNote: string = 'Test private note';
  // New order by customer data
  const orderData: OrderData = new OrderData({
    customer: customerData,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });
  // Customer login data
  const customerLoginData = new CustomerData({
    email: customerData.email,
    password: customerData.password,
  });

  // Pre-Condition: create customer
  createAccountTest(customerData, `${baseContext}_preTest_1`);

  // Pre-condition: Create first address
  createAddressTest(customerLoginData, firstAddressData, `${baseContext}_preTest_2`);

  // Pre-condition: Create second address
  createAddressTest(customerLoginData, secondAddressData, `${baseContext}_preTest_3`);

  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_4`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Get customer ID and address ID
  describe('Get customer ID and second address ID', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

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

    it('should get the customer ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getCustomerID', baseContext);

      customerID = parseInt(await customersPage.getTextColumnFromTableCustomers(page, 1, 'id_customer'), 10);
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
  });

  // 1 - Go to view order page
  describe('View order page', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable2', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });
  });

  // 2 - check customer block
  describe('View customer block', async () => {
    it('should check customer title, name, lastname, reference', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInfo', baseContext);

      const customerInfo = await orderPageCustomerBlock.getCustomerInfoBlock(page);
      await expect(customerInfo).to.contains(customerData.socialTitle);
      await expect(customerInfo).to.contains(customerData.firstName);
      await expect(customerInfo).to.contains(customerData.lastName);
      await expect(customerInfo).to.contains(customerID.toString());
    });

    it('should check customer email address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const customerEmail = await orderPageCustomerBlock.getCustomerEmail(page);
      await expect(customerEmail).to.contains(`mailto:${customerData.email}`);
    });

    it('should check the number of validated orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidatedOrderNumber', baseContext);

      const customerEmail = await orderPageCustomerBlock.getValidatedOrdersNumber(page);
      await expect(customerEmail).to.equal(0);
    });

    it('should check the shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

      const shippingAddress = await orderPageCustomerBlock.getShippingAddress(page);
      await expect(shippingAddress)
        .to.contain(firstAddressData.firstName)
        .and.to.contain(firstAddressData.lastName)
        .and.to.contain(firstAddressData.address)
        .and.to.contain(firstAddressData.postalCode)
        .and.to.contain(firstAddressData.city)
        .and.to.contain(firstAddressData.country);
    });

    it('should check the invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddress', baseContext);

      const shippingAddress = await orderPageCustomerBlock.getInvoiceAddress(page);
      await expect(shippingAddress)
        .to.contain(firstAddressData.firstName)
        .and.to.contain(firstAddressData.lastName)
        .and.to.contain(firstAddressData.address)
        .and.to.contain(firstAddressData.postalCode)
        .and.to.contain(firstAddressData.city)
        .and.to.contain(firstAddressData.country);
    });

    it('should check that private note textarea is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteVisible', baseContext);

      const result = await orderPageCustomerBlock.isPrivateNoteTextareaVisible(page);
      await expect(result).to.be.false;
    });

    it('should click on \'View full details\' and check if the page is redirected to '
      + '\'Customer\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewFullDetails', baseContext);

      await orderPageCustomerBlock.goToViewFullDetails(page);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerData.lastName);
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
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock2', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should edit existing shipping address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editShippingAddress', baseContext);

      const shippingAddress = await orderPageCustomerBlock.editExistingShippingAddress(page, editShippingAddressData);
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

      const alertMessage = await orderPageCustomerBlock.selectAnotherShippingAddress(page, addressToSelect);
      expect(alertMessage).to.contains(orderPageCustomerBlock.successfulUpdateMessage);

      const shippingAddress = await orderPageCustomerBlock.getShippingAddress(page);
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

      const invoiceAddress = await orderPageCustomerBlock.editExistingInvoiceAddress(page, editInvoiceAddressData);
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

      const alertMessage = await orderPageCustomerBlock.selectAnotherInvoiceAddress(page, addressToSelect);
      expect(alertMessage).to.contains(orderPageCustomerBlock.successfulUpdateMessage);

      const shippingAddress = await orderPageCustomerBlock.getInvoiceAddress(page);
      await expect(shippingAddress)
        .to.contain(secondAddressData.firstName)
        .and.to.contain(secondAddressData.lastName)
        .and.to.contain(secondAddressData.address)
        .and.to.contain(secondAddressData.postalCode)
        .and.to.contain(secondAddressData.city)
        .and.to.contain(secondAddressData.country);
    });

    it('should click on add new note and check that the textarea is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextarea', baseContext);

      await orderPageCustomerBlock.clickAddNewPrivateNote(page);

      const result = await orderPageCustomerBlock.isPrivateNoteTextareaVisible(page);
      await expect(result).to.be.true;
    });

    it('should go back to \'Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${Customers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable2', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', Customers.johnDoe.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(Customers.johnDoe.lastName);
    });

    it('should view the 1st order for the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock3', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should add private note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPrivateNote', baseContext);

      await orderPageCustomerBlock.clickAddNewPrivateNote(page);

      const result = await orderPageCustomerBlock.setPrivateNote(page, privateNote);
      await expect(result).to.contains(orderPageCustomerBlock.successfulUpdateMessage);
    });

    it('should go back to \'Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage3', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock5', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should click on add new note and check that the textarea is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextareaVisible', baseContext);

      await orderPageCustomerBlock.clickAddNewPrivateNote(page);

      const result = await orderPageCustomerBlock.isPrivateNoteTextareaVisible(page);
      await expect(result).to.be.true;
    });

    it('should check that the private note is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextNotVisible', baseContext);

      const note = await orderPageCustomerBlock.getPrivateNoteContent(page);
      await expect(note).to.not.equal(privateNote);
    });

    it('should go back to \'Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage4', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${Customers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable4', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', Customers.johnDoe.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(Customers.johnDoe.lastName);
    });

    it('should view the 2nd order for the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock4', baseContext);

      await ordersPage.goToOrder(page, 2);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should check that the private note is not empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextVisible', baseContext);

      const result = await orderPageCustomerBlock.isPrivateNoteTextareaVisible(page);
      await expect(result).to.be.true;

      const note = await orderPageCustomerBlock.getPrivateNoteContent(page);
      await expect(note).to.equal(privateNote);
    });

    it('should delete private note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePrivateNote', baseContext);

      const result = await orderPageCustomerBlock.setPrivateNote(page, '');
      await expect(result).to.contains(orderPageCustomerBlock.successfulUpdateMessage);
    });
  });

  // Post-condition: Delete the created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
});
