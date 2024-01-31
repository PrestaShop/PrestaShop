// Import utils
import date from '@utils/date';
import files from '@utils/files';
import helper from '@utils/helpers';
import basicHelper from '@utils/basicHelper';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import customersPage from '@pages/BO/customers';
import viewCustomerPage from '@pages/BO/customers/view';
import customerServicePage from '@pages/BO/customerService/customerService';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import checkoutPage from '@pages/FO/classic/checkout';
import orderConfirmationPage from '@pages/FO/classic/checkout/orderConfirmation';
import {contactUsPage} from '@pages/FO/classic/contactUs';
import {homePage} from '@pages/FO/classic/home';
import {loginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {createAccountPage} from '@pages/FO/classic/myAccount/add';
import gdprPersonalDataPage from '@pages/FO/classic/myAccount/gdprPersonalData';
import productPage from '@pages/FO/classic/product';

// Import data
import AddressData from '@data/faker/address';
import MessageData from '@data/faker/message';
import CustomerData from '@data/faker/customer';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_userAccount_getGDPRDataInPDF';

/*
Scenario
- Check GDPR PDF file after create customer and first login
- Check GDPR PDF file after create a cart
- Check GDPR PDF file after create an order and an address
- Check GDPR PDF file after send a message
- Check GDPR PDF file after logout and login in FO
Post condition:
- Delete created customer
 */
describe('FO - Account : Get GDPR data in PDF', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;
  let registrationDate: string;
  let lastVisitDate: string;
  let secondLastVisitDate: string;
  let numberOfShoppingCarts: number;
  let shoppingCartID: string;
  let shoppingCartDate: string;
  let orderReference: string = '';
  let totalPaid: number;
  let orderDate: string;
  let messageDate: string;
  let ipAddress: string;
  let connectionOrigin: string;

  const customerData: CustomerData = new CustomerData({
    firstName: 'Marc',
    lastName: 'Beier',
    email: 'presta@prestashop.com',
  });
  const today: string = date.getDateFormat('mm/dd/yyyy');
  const dateNow: Date = new Date();
  const addressData: AddressData = new AddressData({
    alias: 'My Address',
    firstName: 'Marc',
    lastName: 'Beier',
    country: 'France',
    address: '17, Main street',
    city: 'Paris',
    company: 'PrestaShop',
  });
  const contactUsData: MessageData = new MessageData({
    firstName: customerData.firstName,
    lastName: customerData.lastName,
    subject: 'Customer service',
    message: 'Message test',
    emailAddress: customerData.email,
    reference: orderReference,
  });

  const createCustomerName: string = `${customerData.firstName[0]}. ${customerData.lastName}`;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    // Create file for contact us form
    await files.createFile('.', `${contactUsData.fileName}.txt`, 'new filename');
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    // Delete the created file
    await files.deleteFile(`${contactUsData.fileName}.txt`);
  });

  describe('Check GDPR PDF file after create customer and first login', async () => {
    describe('Create account on FO and download GDPR - Personal data PDF', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount1', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to create account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

        await homePage.goToLoginPage(page);
        await loginPage.goToCreateAccountPage(page);

        const pageHeaderTitle = await createAccountPage.getHeaderTitle(page);
        expect(pageHeaderTitle).to.equal(createAccountPage.formTitle);
      });

      it('should create new account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

        await createAccountPage.createAccount(page, customerData);

        const isCustomerConnected = await homePage.isCustomerConnected(page);
        expect(isCustomerConnected).to.eq(true);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage1', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage1', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to PDF file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToPDF1', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToPDF(page);

        const found = await files.doesFileExist(filePath);
        expect(found, 'PDF file was not downloaded').to.eq(true);
      });
    });

    describe('Get personal information from BO', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Customers > Customers\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage1', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customersParentLink,
          dashboardPage.customersLink,
        );
        await customersPage.closeSfToolBar(page);

        const pageTitle = await customersPage.getPageTitle(page);
        expect(pageTitle).to.contains(customersPage.pageTitle);
      });

      it(`should filter by customer first name '${customerData.firstName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomerFirstName1', baseContext);

        await customersPage.filterCustomers(page, 'input', 'firstname', customerData.firstName);

        const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterFilter).to.equal(1);
      });

      it('should get creation account date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getRegistrationDate', baseContext);

        const registration = await customersPage.getTextColumnFromTableCustomers(page, 1, 'date_add');
        registrationDate = `${registration.substring(6, 10)}-${registration.substring(0, 2)}-`
          + `${registration.substring(3, 5)} ${registration.substring(11, 19)}`;
        expect(registrationDate).to.contains(dateNow.getFullYear());
      });

      it('should get last visit date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getLastVisitDate1', baseContext);

        const lastVisit = await customersPage.getTextColumnFromTableCustomers(page, 1, 'connect');
        lastVisitDate = `${lastVisit.substring(6, 10)}-${lastVisit.substring(0, 2)}-`
          + `${lastVisit.substring(3, 5)} ${lastVisit.substring(11, 19)}`;
        expect(lastVisitDate).to.contains(dateNow.getFullYear());
      });

      it('should click on view customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);

        await customersPage.goToViewCustomerPage(page, 1);

        const pageTitle = await viewCustomerPage.getPageTitle(page);
        expect(pageTitle).to.contains(viewCustomerPage.pageTitle(createCustomerName));
      });

      it('should get last connections ip address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLAstConnections', baseContext);

        ipAddress = await viewCustomerPage.getTextColumnFromTableLastConnections(page, 'ip-address');
        expect(ipAddress).to.not.eq(null);
      });
    });

    describe('Check GDPR data in PDF', async () => {
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/22581
      it('should check the logo in PDF File', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductImage', baseContext);

        this.skip();

        const imageNumber = await files.getImageNumberInPDF(filePath);
        expect(imageNumber).to.be.equal(1);
      });

      it('should check the date and the customer name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDateAndCustomerName', baseContext);

        const isVisible = await files.isTextInPDF(
          filePath,
          `${today},,${customerData.firstName} ${customerData.lastName},,`,
        );
        expect(isVisible, 'The date and the customer name are not correct!').to.eq(true);
      });

      it('should check general info', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkGeneralInfo', baseContext);

        const age = await basicHelper.age(customerData.birthDate);

        const isVisible = await files.isTextInPDF(filePath, 'General info,,Gender, ,'
          + `${customerData.socialTitle},Name, ,${customerData.firstName} ${customerData.lastName},`
          + `Birth date, ,${customerData.birthDate.toISOString().slice(0, 10)},Age, ,${age},Email,`
          + `${customerData.email},Language, ,English (English),Creation account date, ,`
          + `${registrationDate},Last visit, ,${lastVisitDate},Siret,Ape,Company,Website`);
        expect(isVisible, 'General info is not correct!').to.eq(true);
      });

      it('should check that Addresses table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatAddressesTableIsEmpty', baseContext);

        const isVisible = await files.isTextInPDF(filePath, ',No addresses,,');
        expect(isVisible, 'Addresses table is not empty!').to.eq(true);
      });

      it('should check that Orders table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatOrdersTableIsEmpty', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Orders,,Reference, ,Payment, ,Order state, ,'
          + 'Total paid, ,Date,,No orders,,');
        expect(isVisible, 'Orders table is not empty!').to.eq(true);
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatCartsTableIsEmpty1', baseContext);

        const isVisible = await files.isTextInPDF(filePath, ',Carts,,Id, ,Total products, ,Date,,No carts,,');
        expect(isVisible, 'Carts table is not empty!').to.eq(true);
      });

      it('should check that Messages table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatMessagesTableIsEmpty', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Messages,,IP, ,Message, ,Date,,No messages,,');
        expect(isVisible, 'Messages table is not empty!').to.eq(true);
      });

      it('should check Last connections table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLastConnectionsTable1', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Last connections,,Origin request, ,Page viewed, ,'
          + `Time on the page, ,IP address, ,Date,,1 / 2 ${today},,${customerData.firstName} `
          + `${customerData.lastName},,0, ,${ipAddress}, ,${lastVisitDate}`);
        expect(isVisible, 'The data in Last connections table is not correct!').to.eq(true);
      });

      it('should check that Newsletter subscription table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNewsletterSubscriptionTable', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Module : Newsletter subscription,,0,,Newsletter '
          + 'subscription: no email to export, this customer has not registered.,,');
        expect(isVisible, 'Newsletter subscription table is not empty!').to.eq(true);
      });

      it('should check that Module product comments is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkModuleProductComments', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Module : Product Comments,,');
        expect(isVisible, 'Products comments is not empty!').to.eq(true);
      });

      it('should check that mail alerts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkModuleMailAlerts', baseContext);

        const isVisible = await files.isTextInPDF(filePath, ',Module : Mail alerts,,0,,Mail alert: Unable to export '
          + 'customer using email.');
        expect(isVisible, 'Mail alert table is not empty!').to.eq(true);
      });
    });
  });

  describe('Check GDPR PDF file after create a cart', async () => {
    describe('Add a product to the cart and download PDF file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount2', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        // Go to the first product page
        await homePage.goToProductPage(page, 1);
        // Add the product to the cart
        await productPage.addProductToTheCart(page, 2);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(2);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage2', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage2', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to PDF file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToPDF2', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToPDF(page);

        const found = await files.doesFileExist(filePath);
        expect(found, 'PDF file was not downloaded').to.eq(true);
      });
    });

    describe('Get shopping cart data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage1', baseContext);

        await loginPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to \'Orders > Shopping carts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.shoppingCartsLink,
        );

        const pageTitle = await shoppingCartsPage.getPageTitle(page);
        expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
      });

      it('should reset all filters and get number of shopping carts', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

        numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
        expect(numberOfShoppingCarts).to.be.above(0);
      });

      it('should filter list by customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

        await shoppingCartsPage.filterTable(page, 'input', 'c!lastname', customerData.lastName);

        const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
        expect(numberOfShoppingCartsAfterFilter).to.equal(1);

        const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'c!lastname');
        expect(textColumn).to.contains(customerData.lastName);
      });

      it('should get shopping cart ID and Date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getShoppingCartIDAndDate', baseContext);

        shoppingCartDate = await shoppingCartsPage.getTextColumn(page, 1, 'date');

        shoppingCartID = await shoppingCartsPage.getTextColumn(page, 1, 'id_cart');
        expect(parseInt(shoppingCartID, 10)).to.be.greaterThan(5);
      });
    });

    describe('Check GDPR data in PDF', async () => {
      it('should check Carts table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatCartsTableIsEmpty2', baseContext);

        shoppingCartDate = `${shoppingCartDate.substring(6, 10)}-${shoppingCartDate.substring(0, 2)}-`
          + `${shoppingCartDate.substring(3, 5)} ${shoppingCartDate.substring(11, 19)}`;

        const isVisible = await files.isTextInPDF(filePath, `Carts,,Id, ,Total products, ,Date,,#${shoppingCartID}`
          + `, ,1, ,${shoppingCartDate},,Product(s) in the cart :,,Reference, ,Name, ,Quantity,,`
          + `${Products.demo_1.reference}, ,${Products.demo_1.name}, ,2`);
        expect(isVisible, 'Data in Carts table is not correct!').to.eq(true);
      });
    });
  });

  describe('Check GDPR PDF file after create an order and an address', async () => {
    describe('Create an order and download PDF file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount3', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to carts page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        await homePage.goToCartPage(page);

        const pageTitle = await cartPage.getPageTitle(page);
        expect(pageTitle).to.contains(cartPage.pageTitle);
      });

      it('should fill address form and go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage3', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage3', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to PDF file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToPDF3', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToPDF(page);

        const found = await files.doesFileExist(filePath);
        expect(found, 'PDF file was not downloaded').to.eq(true);
      });
    });

    describe('Get created order data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage2', baseContext);

        await loginPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should filter the Orders table by customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrdersTable', baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

        const numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
        expect(numberOfOrdersAfterFilter).to.equal(1);

        const textColumn = await ordersPage.getTextColumn(page, 'customer');
        expect(textColumn).to.contains(customerData.lastName);
      });

      it('should get order data', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderData', baseContext);

        orderReference = await ordersPage.getTextColumn(page, 'reference');
        expect(orderReference).to.not.eq(null);

        totalPaid = await ordersPage.getOrderATIPrice(page);
        orderDate = await ordersPage.getTextColumn(page, 'date_add');
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetOrdersTable', baseContext);

        const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
        expect(numberOfOrders).to.be.above(0);
      });
    });

    describe('Check GDPR data in PDF', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable1', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Addresses,,Alias, ,Company, ,Name, ,Address, ,'
          + `Phone(s), ,Country, ,Date,,${addressData.alias}, ,${addressData.company}, ,${addressData.firstName} `
          + `${addressData.lastName}, ,${addressData.address},${addressData.postalCode} ${addressData.city},`
          + `${addressData.phone}, ,${addressData.country}`);
        expect(isVisible, 'Data in Addresses table is not correct!').to.eq(true);
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable1', baseContext);

        const orderCreateDate = `${orderDate.substring(6, 10)}-${orderDate.substring(0, 2)}-`
          + `${orderDate.substring(3, 5)} ${orderDate.substring(11, 19)}`;
        const isVisible = await files.isTextInPDF(filePath, 'Orders,,Reference, ,Payment, ,Order state, ,Total paid,'
          + ` ,Date,,${orderReference}, ,Bank transfer, ,Awaiting bank wire,payment,${totalPaid} EUR, ,`
          + `${orderCreateDate},,Product(s) in the order :,,Reference, ,Name, ,Quantity,,${Products.demo_1.reference}`
          + `, ,${Products.demo_1.name},(Size: S - Color: White),2`);
        expect(isVisible, 'Data in Orders table is not correct!').to.eq(true);
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable1', baseContext);

        const isVisible = await files.isTextInPDF(filePath, ',Carts,,Id, ,Total products, ,Date,,No carts');
        expect(isVisible, 'Carts table is not empty!').to.eq(true);
      });
    });
  });

  describe('Check GDPR PDF file after send a message', async () => {
    describe('Send message and download PDF file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount4', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go on contact us page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

        // Go to contact us page
        await loginPage.goToFooterLink(page, 'Contact us');

        const pageTitle = await contactUsPage.getPageTitle(page);
        expect(pageTitle).to.equal(contactUsPage.pageTitle);
      });

      it('should send message to customer service', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

        await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

        const validationMessage = await contactUsPage.getAlertSuccess(page);
        expect(validationMessage).to.equal(contactUsPage.validationMessage);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage4', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage4', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to PDF file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToPDF4', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToPDF(page);

        const found = await files.doesFileExist(filePath);
        expect(found, 'PDF file was not downloaded').to.eq(true);
      });
    });

    describe('Get message data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage3', baseContext);

        await loginPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to customer service page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customerServiceParentLink,
          dashboardPage.customerServiceLink,
        );

        const pageTitle = await customerServicePage.getPageTitle(page);
        expect(pageTitle).to.contains(customerServicePage.pageTitle);
      });

      it('should check customer name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerName', baseContext);

        const email = await customerServicePage.getTextColumn(page, 1, 'customer');
        expect(email).to.contain(`${contactUsData.firstName} ${contactUsData.lastName}`);
      });

      it('should get last message date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

        messageDate = await customerServicePage.getTextColumn(page, 1, 'date');
        expect(messageDate).to.not.eq(null);
      });
    });

    describe('Check GDPR data in PDF', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable2', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Addresses,,Alias, ,Company, ,Name, ,Address, ,'
          + `Phone(s), ,Country, ,Date,,My Address, ,${addressData.company}, ,${addressData.firstName} `
          + `${addressData.lastName}, ,${addressData.address},${addressData.postalCode} ${addressData.city},`
          + `${addressData.phone}, ,${addressData.country}`);
        expect(isVisible, 'Data in Addresses table is not correct!').to.eq(true);
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable2', baseContext);

        const orderCreateDate = `${orderDate.substring(6, 10)}-${orderDate.substring(0, 2)}-`
          + `${orderDate.substring(3, 5)} ${orderDate.substring(11, 19)}`;
        const isVisible = await files.isTextInPDF(filePath, 'Orders,,Reference, ,Payment, ,Order state, ,Total paid,'
          + ` ,Date,,${orderReference}, ,Bank transfer, ,Awaiting bank wire,payment,${totalPaid} EUR, ,`
          + `${orderCreateDate},,Product(s) in the order :,,Reference, ,Name, ,Quantity,,${Products.demo_1.reference}`
          + `, ,${Products.demo_1.name},(Size: S - Color: White),2`);
        expect(isVisible, 'Data in Orders table is not correct!').to.eq(true);
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable2', baseContext);

        const isVisible = await files.isTextInPDF(filePath, ',Carts,,Id, ,Total products, ,Date,,No carts');
        expect(isVisible, 'Carts table is not empty!').to.eq(true);
      });

      it('should check Messages table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkMessagesTable1', baseContext);

        const dateString = `${messageDate.substring(6, 10)}-${messageDate.substring(0, 2)}-`
          + `${messageDate.substring(3, 5)} ${messageDate.substring(11, 19)}`;
        const isVisible = await files.isTextInPDF(filePath, `Messages,,IP, ,Message, ,Date,,1 / 2 ${today},,`
          + `${contactUsData.firstName} ${contactUsData.lastName},,${ipAddress}, ,${contactUsData.message}, ,${
            dateString}`);
        expect(isVisible, 'Data in Messages table is not correct!').to.eq(true);
      });
    });
  });

  describe('Check GDPR PDF file after logout and login in FO', async () => {
    describe('Logout then login and download PDF file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount5', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should logout by the link in the header', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByHeaderLink', baseContext);

        await homePage.logout(page);

        const isCustomerConnected = await homePage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
      });

      it('should sign in', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInFO2', baseContext);

        await homePage.goToLoginPage(page);
        await loginPage.customerLogin(page, customerData);

        const isCustomerConnected = await loginPage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage5', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage5', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to PDF file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToPDF5', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToPDF(page);

        const found = await files.doesFileExist(filePath);
        expect(found, 'PDF file was not downloaded').to.eq(true);
      });
    });

    describe('Get last customer connection data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage4', baseContext);

        await loginPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to \'Customers > Customers\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage2', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customersParentLink,
          dashboardPage.customersLink,
        );
        await customersPage.closeSfToolBar(page);

        const pageTitle = await customersPage.getPageTitle(page);
        expect(pageTitle).to.contains(customersPage.pageTitle);
      });

      it(`should filter by customer first name '${customerData.firstName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomerFirstName2', baseContext);

        await customersPage.filterCustomers(page, 'input', 'firstname', customerData.firstName);

        const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterFilter).to.equal(1);
      });

      it('should get last visit date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getLastVisitDate2', baseContext);

        const lastVisit = await customersPage.getTextColumnFromTableCustomers(page, 1, 'connect');
        secondLastVisitDate = `${lastVisit.substring(6, 10)}-${lastVisit.substring(0, 2)}-`
          + `${lastVisit.substring(3, 5)} ${lastVisit.substring(11, 19)}`;
        expect(lastVisitDate).to.contains(dateNow.getFullYear());
      });

      it('should click on view customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage2', baseContext);

        await customersPage.goToViewCustomerPage(page, 1);

        const pageTitle = await viewCustomerPage.getPageTitle(page);
        expect(pageTitle).to.contains(viewCustomerPage.pageTitle(createCustomerName));
      });

      it('should get last connections origin', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLastConnectionsOrigin', baseContext);

        connectionOrigin = await viewCustomerPage.getTextColumnFromTableLastConnections(page, 'origin', 1);
        if (connectionOrigin === 'Direct link') {
          connectionOrigin = '';
        } else if (connectionOrigin === 'localhost') {
          connectionOrigin = `${global.INSTALL.ENABLE_SSL ? 'https://localhost:8002' : 'http://localhost:8001'},/en/,`;
        }
        expect(connectionOrigin).to.not.eq(null);
      });
    });

    describe('Check GDPR data in PDF', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable3', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Addresses,,Alias, ,Company, ,Name, ,Address, ,'
          + `Phone(s), ,Country, ,Date,,My Address, ,${addressData.company}, ,${addressData.firstName} `
          + `${addressData.lastName}, ,${addressData.address},${addressData.postalCode} ${addressData.city},`
          + `${addressData.phone}, ,${addressData.country}`);
        expect(isVisible, 'Data in Addresses table is not correct!').to.eq(true);
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable3', baseContext);

        const orderCreateDate = `${orderDate.substring(6, 10)}-${orderDate.substring(0, 2)}-`
          + `${orderDate.substring(3, 5)} ${orderDate.substring(11, 19)}`;
        const isVisible = await files.isTextInPDF(filePath, 'Orders,,Reference, ,Payment, ,Order state, ,Total paid,'
          + ` ,Date,,${orderReference}, ,Bank transfer, ,Awaiting bank wire,payment,${totalPaid} EUR, ,`
          + `${orderCreateDate},,Product(s) in the order :,,Reference, ,Name, ,Quantity,,${Products.demo_1.reference}`
          + `, ,${Products.demo_1.name},(Size: S - Color: White),2`);
        expect(isVisible, 'Data in Orders table is not correct!').to.eq(true);
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable3', baseContext);

        const isVisible = await files.isTextInPDF(filePath, ',Carts,,Id, ,Total products, ,Date,,No carts');
        expect(isVisible, 'Carts table is not empty!').to.eq(true);
      });

      it('should check Messages table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkMessagesTable2', baseContext);

        const dateString = `${messageDate.substring(6, 10)}-${messageDate.substring(0, 2)}-`
          + `${messageDate.substring(3, 5)} ${messageDate.substring(11, 19)}`;
        const isVisible = await files.isTextInPDF(filePath, `Messages,,IP, ,Message, ,Date,,1 / 2 ${today},,`
          + `${contactUsData.firstName} ${contactUsData.lastName},,${ipAddress}, ,${contactUsData.message}, ,${
            dateString}`);
        expect(isVisible, 'Data in Messages table is not correct!').to.eq(true);
      });

      it('should check Last connections table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLastConnectionsTable2', baseContext);

        const isVisible = await files.isTextInPDF(filePath, 'Last connectionsOrigin request Page viewed '
          + `Time on the page IP address Date${connectionOrigin.split(',').join('')}0 ${ipAddress} ${secondLastVisitDate}0 `
          + `${ipAddress} ${lastVisitDate}`, true);
        expect(isVisible, 'The data in Last connections table is not correct!').to.eq(true);
      });
    });
  });

  // Post-condition: Create new account on FO
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
