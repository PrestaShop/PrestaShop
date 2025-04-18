import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest, createAddressTest} from '@commonTests/FO/classic/account';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boAddressesPage,
  boCustomersPage,
  boCustomersViewPage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockCustomersPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const customerData: FakerCustomer = new FakerCustomer();
  const firstAddressData: FakerAddress = new FakerAddress({firstName: 'first', country: 'France'});
  const secondAddressData: FakerAddress = new FakerAddress({firstName: 'second', country: 'France'});
  const editShippingAddressData: FakerAddress = new FakerAddress({country: 'France'});
  const editInvoiceAddressData: FakerAddress = new FakerAddress({country: 'France'});
  const privateNote: string = 'Test private note';
  // New order by customer data
  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  // Customer login data
  const customerLoginData = new FakerCustomer({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition: Get customer ID and address ID
  describe('Get customer ID and second address ID', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.customersLink,
      );
      await boCustomersPage.closeSfToolBar(page);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCustomersPage.filterCustomers(page, 'input', 'email', customerData.email);

      const textResult = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textResult).to.contains(customerData.email);
    });

    it('should get the customer ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getCustomerID', baseContext);

      customerID = parseInt(await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'id_customer'), 10);
      expect(customerID).to.be.above(0);
    });

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should get the customer address ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getCustomerAddressID', baseContext);

      await boAddressesPage.filterAddresses(page, 'input', 'firstname', secondAddressData.firstName);

      const numberOfAddressesAfterFilter = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddressesAfterFilter).to.be.at.most(1);

      addressID = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address');
    });
  });

  // 1 - Go to view order page
  describe('View order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable2', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock1', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockCustomersPage.pageTitle);
    });
  });

  // 2 - check customer block
  describe('View customer block', async () => {
    it('should check customer title, name, lastname, id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerInfo', baseContext);

      const customerInfo = await boOrdersViewBlockCustomersPage.getCustomerInfoBlock(page);
      expect(customerInfo).to.contains(customerData.socialTitle);
      expect(customerInfo).to.contains(customerData.firstName);
      expect(customerInfo).to.contains(customerData.lastName);

      const customerId = await boOrdersViewBlockCustomersPage.getCustomerID(page);
      expect(customerId).to.equal(customerID);
    });

    it('should check customer email address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const customerEmail = await boOrdersViewBlockCustomersPage.getCustomerEmail(page);
      expect(customerEmail).to.contains(`mailto:${customerData.email}`);
    });

    it('should check the number of validated orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkValidatedOrderNumber', baseContext);

      const customerEmail = await boOrdersViewBlockCustomersPage.getValidatedOrdersNumber(page);
      expect(customerEmail).to.equal(0);
    });

    it('should check the shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

      const shippingAddress = await boOrdersViewBlockCustomersPage.getShippingAddress(page);
      expect(shippingAddress)
        .to.contain(firstAddressData.firstName)
        .and.to.contain(firstAddressData.lastName)
        .and.to.contain(firstAddressData.address)
        .and.to.contain(firstAddressData.postalCode)
        .and.to.contain(firstAddressData.city)
        .and.to.contain(firstAddressData.country);
    });

    it('should check the invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddress', baseContext);

      const shippingAddress = await boOrdersViewBlockCustomersPage.getInvoiceAddress(page);
      expect(shippingAddress)
        .to.contain(firstAddressData.firstName)
        .and.to.contain(firstAddressData.lastName)
        .and.to.contain(firstAddressData.address)
        .and.to.contain(firstAddressData.postalCode)
        .and.to.contain(firstAddressData.city)
        .and.to.contain(firstAddressData.country);
    });

    it('should check that private note textarea is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteVisible', baseContext);

      const result = await boOrdersViewBlockCustomersPage.isPrivateNoteTextareaVisible(page);
      expect(result).to.eq(false);
    });

    it('should click on \'View full details\' and check if the page is redirected to '
      + '\'Customer\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewFullDetails', baseContext);

      await boOrdersViewBlockCustomersPage.goToViewFullDetails(page);

      const pageTitle = await boCustomersViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(customerData.lastName);
    });

    it('should go back to Orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock2', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockCustomersPage.pageTitle);
    });

    it('should edit existing shipping address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editShippingAddress', baseContext);

      const shippingAddress = await boOrdersViewBlockCustomersPage.editExistingShippingAddress(page, editShippingAddressData);
      expect(shippingAddress)
        .to.contain(editShippingAddressData.firstName)
        .and.to.contain(editShippingAddressData.lastName)
        .and.to.contain(editShippingAddressData.address)
        .and.to.contain(editShippingAddressData.postalCode)
        .and.to.contain(editShippingAddressData.city)
        .and.to.contain(editShippingAddressData.country);
    });

    it('should select another shipping address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectAnotherShippingAddress', baseContext);

      const addressToSelect = `#${addressID} ${secondAddressData.alias} - ${secondAddressData.address} `
        + `${secondAddressData.secondAddress} ${secondAddressData.postalCode} ${secondAddressData.city}`;

      const alertMessage = await boOrdersViewBlockCustomersPage.selectAnotherShippingAddress(page, addressToSelect);
      expect(alertMessage).to.contains(boOrdersViewBlockCustomersPage.successfulUpdateMessage);

      const shippingAddress = await boOrdersViewBlockCustomersPage.getShippingAddress(page);
      expect(shippingAddress)
        .to.contain(secondAddressData.firstName)
        .and.to.contain(secondAddressData.lastName)
        .and.to.contain(secondAddressData.address)
        .and.to.contain(secondAddressData.postalCode)
        .and.to.contain(secondAddressData.city)
        .and.to.contain(secondAddressData.country);
    });

    it('should edit existing invoice address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editInvoiceAddress', baseContext);

      const invoiceAddress = await boOrdersViewBlockCustomersPage.editExistingInvoiceAddress(page, editInvoiceAddressData);
      expect(invoiceAddress)
        .to.contain(editInvoiceAddressData.firstName)
        .and.to.contain(editInvoiceAddressData.lastName)
        .and.to.contain(editInvoiceAddressData.address)
        .and.to.contain(editInvoiceAddressData.postalCode)
        .and.to.contain(editInvoiceAddressData.city)
        .and.to.contain(editInvoiceAddressData.country);
    });

    it('should select another invoice address and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectAnotherInvoiceAddress', baseContext);

      const addressToSelect = `#${addressID} ${secondAddressData.alias} - ${secondAddressData.address} `
        + `${secondAddressData.secondAddress} ${secondAddressData.postalCode} ${secondAddressData.city}`;

      const alertMessage = await boOrdersViewBlockCustomersPage.selectAnotherInvoiceAddress(page, addressToSelect);
      expect(alertMessage).to.contains(boOrdersViewBlockCustomersPage.successfulUpdateMessage);

      const shippingAddress = await boOrdersViewBlockCustomersPage.getInvoiceAddress(page);
      expect(shippingAddress)
        .to.contain(secondAddressData.firstName)
        .and.to.contain(secondAddressData.lastName)
        .and.to.contain(secondAddressData.address)
        .and.to.contain(secondAddressData.postalCode)
        .and.to.contain(secondAddressData.city)
        .and.to.contain(secondAddressData.country);
    });

    it('should click on add new note and check that the textarea is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextarea', baseContext);

      await boOrdersViewBlockCustomersPage.clickAddNewPrivateNote(page);

      const result = await boOrdersViewBlockCustomersPage.isPrivateNoteTextareaVisible(page);
      expect(result).to.eq(true);
    });

    it('should go back to \'Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable2', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the 1st order for the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock3', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockCustomersPage.pageTitle);
    });

    it('should add private note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPrivateNote', baseContext);

      await boOrdersViewBlockCustomersPage.clickAddNewPrivateNote(page);

      const result = await boOrdersViewBlockCustomersPage.setPrivateNote(page, privateNote);
      expect(result).to.contains(boOrdersViewBlockCustomersPage.successfulUpdateMessage);
    });

    it('should go back to \'Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage3', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable3', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock5', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockCustomersPage.pageTitle);
    });

    it('should click on add new note and check that the textarea is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextareaVisible', baseContext);

      await boOrdersViewBlockCustomersPage.clickAddNewPrivateNote(page);

      const result = await boOrdersViewBlockCustomersPage.isPrivateNoteTextareaVisible(page);
      expect(result).to.eq(true);
    });

    it('should check that the private note is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextNotVisible', baseContext);

      const note = await boOrdersViewBlockCustomersPage.getPrivateNoteContent(page);
      expect(note).to.not.equal(privateNote);
    });

    it('should go back to \'Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrdersPage4', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderTable4', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the 2nd order for the default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock4', baseContext);

      await boOrdersPage.goToOrder(page, 2);

      const pageTitle = await boOrdersViewBlockCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockCustomersPage.pageTitle);
    });

    it('should check that the private note is not empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPrivateNoteTextVisible', baseContext);

      const result = await boOrdersViewBlockCustomersPage.isPrivateNoteTextareaVisible(page);
      expect(result).to.eq(true);

      const note = await boOrdersViewBlockCustomersPage.getPrivateNoteContent(page);
      expect(note).to.equal(privateNote);
    });

    it('should delete private note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePrivateNote', baseContext);

      const result = await boOrdersViewBlockCustomersPage.setPrivateNote(page, '');
      expect(result).to.contains(boOrdersViewBlockCustomersPage.successfulUpdateMessage);
    });
  });

  // Post-condition: Delete the created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
});
