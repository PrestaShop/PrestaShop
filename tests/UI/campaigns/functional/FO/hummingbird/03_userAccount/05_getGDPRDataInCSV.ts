import testContext from '@utils/testContext';
import {expect} from 'chai';

import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {
  boCustomersPage,
  boCustomersViewPage,
  boCustomerServicePage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boShoppingCartsPage,
  type BrowserContext,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerContactMessage,
  FakerCustomer,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdCheckoutOrderConfirmationPage,
  foHummingbirdContactUsPage,
  foHummingbirdCreateAccountPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyGDPRPersonalDataPage,
  foHummingbirdProductPage,
  type GDPRExportCSV,
  modPsGdprBoMain,
  type Page,
  utilsCore,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_userAccount_getGDPRDataInCSV';

/*
Scenario:
- Check GDPR CSV file after create customer and first login
- Check GDPR CSV file after create a cart
- Check GDPR CSV file after create an order and an address
- Check GDPR CSV file after send a message
- Check GDPR CSV file after logout and login in FO
Post condition:
- Delete created customer
 */
describe('FO - Account : Get GDPR data in CSV', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string | null;
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
  let csvData: GDPRExportCSV[] = [];

  const customerData: FakerCustomer = new FakerCustomer({
    firstName: 'Marc',
    lastName: 'Beier',
    email: 'presta@prestashop.com',
  });
  const date: Date = new Date();
  const addressData: FakerAddress = new FakerAddress({
    firstName: 'Marc',
    lastName: 'Beier',
    country: 'France',
    address: '17, Main street',
    city: 'Paris',
    company: 'PrestaShop',
  });
  const contactUsData: FakerContactMessage = new FakerContactMessage({
    firstName: customerData.firstName,
    lastName: customerData.lastName,
    subject: 'Customer service',
    message: 'Message test',
    emailAddress: customerData.email,
    reference: orderReference,
  });

  const createCustomerName: string = `${customerData.firstName[0]}. ${customerData.lastName}`;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    // Create file for contact us form
    await utilsFile.createFile('.', `${contactUsData.fileName}.txt`, 'new filename');
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    // Delete the created file
    await utilsFile.deleteFile(`${contactUsData.fileName}.txt`);
  });

  describe('Check GDPR CSV file after create customer and first login', async () => {
    describe('Create account on FO and download GDPR - Personal data CSV', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount1', baseContext);

        await foHummingbirdHomePage.goToFo(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to create account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

        await foHummingbirdHomePage.goToLoginPage(page);
        await foHummingbirdLoginPage.goToCreateAccountPage(page);

        const pageHeaderTitle = await foHummingbirdCreateAccountPage.getHeaderTitle(page);
        expect(pageHeaderTitle).to.equal(foHummingbirdCreateAccountPage.formTitle);
      });

      it('should create new account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

        await foHummingbirdCreateAccountPage.createAccount(page, customerData);

        const isCustomerConnected = await foHummingbirdHomePage.isCustomerConnected(page);
        expect(isCustomerConnected).to.eq(true);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage1', baseContext);

        await foHummingbirdHomePage.goToMyAccountPage(page);

        const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage1', baseContext);

        await foHummingbirdMyAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await foHummingbirdMyGDPRPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyGDPRPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV1', baseContext);

        filePath = await foHummingbirdMyGDPRPersonalDataPage.exportDataToCSV(page);

        const found = await utilsFile.doesFileExist(filePath);
        expect(found, 'CSV file was not downloaded').to.eq(true);
      });
    });

    describe('Get personal information from BO', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Customers > Customers\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage1', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customersParentLink,
          boDashboardPage.customersLink,
        );
        await boCustomersPage.closeSfToolBar(page);

        const pageTitle = await boCustomersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomersPage.pageTitle);
      });

      it(`should filter by customer first name '${customerData.firstName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomerFirstName1', baseContext);

        await boCustomersPage.filterCustomers(page, 'input', 'firstname', customerData.firstName);

        const numberOfCustomersAfterFilter = await boCustomersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterFilter).to.equal(1);
      });

      it('should get creation account date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getRegistrationDate', baseContext);

        const registration = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'date_add');

        registrationDate = `${registration.substring(6, 10)}-${registration.substring(0, 2)}-`
          + `${registration.substring(3, 5)} ${registration.substring(11, 19)}`;
        expect(registrationDate).to.contains(date.getFullYear());
      });

      it('should get last visit date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getLastVisitDate1', baseContext);

        const lastVisit = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'connect');
        lastVisitDate = `${lastVisit.substring(6, 10)}-${lastVisit.substring(0, 2)}-`
          + `${lastVisit.substring(3, 5)} ${lastVisit.substring(11, 19)}`;
        expect(lastVisitDate).to.contains(date.getFullYear());
      });

      it('should click on view customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);

        await boCustomersPage.goToViewCustomerPage(page, 1);

        const pageTitle = await boCustomersViewPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomersViewPage.pageTitle(createCustomerName));
      });

      it('should get last connections ip address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLAstConnections', baseContext);

        ipAddress = await boCustomersViewPage.getTextColumnFromTableLastConnections(page, 'ip-address');
        expect(ipAddress).to.not.eq(null);
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check general info', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkGeneralInfo', baseContext);

        const age = (await utilsCore.age(customerData.birthDate)).toString();

        csvData = await modPsGdprBoMain.parseCSVFile(filePath, 'utf16le');
        expect(csvData.length).to.gt(0);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'GENERAL INFO');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('GENERAL INFO');
        expect(csvDatum[0].rows.length).to.equal(1);
        expect(csvDatum[0].rows[0]).to.deep.equal([
          customerData.socialTitle,
          `${customerData.firstName} ${customerData.lastName}`,
          customerData.birthDate.toISOString().slice(0, 10),
          age,
          customerData.email,
          'English (English)',
          registrationDate,
          lastVisitDate,
          '',
          '',
          '',
          '',
        ]);
      });

      it('should check that Addresses table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatAddressesTableIsEmpty', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'ADDRESSES');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('ADDRESSES');
        expect(csvDatum[0].error).to.equal('No addresses');
      });

      it('should check that Orders table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatOrdersTableIsEmpty', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'ORDERS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('ORDERS');
        expect(csvDatum[0].error).to.equal('No orders');
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatCartsTableIsEmpty1', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'CARTS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('CARTS');
        expect(csvDatum[0].error).to.equal('No carts');
      });

      it('should check that Messages table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatMessagesTableIsEmpty', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'MESSAGES');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('MESSAGES');
        expect(csvDatum[0].error).to.equal('No messages');
      });

      it('should check Last connections table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLastConnectionsTable1', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'LAST CONNECTIONS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('LAST CONNECTIONS');
        expect(csvDatum[0].rows[0]).to.deep.equal([
          '',
          '0',
          '',
          ipAddress,
          lastVisitDate,
        ]);
      });

      it('should check that Newsletter subscription table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNewsletterSubscriptionTable', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'MODULE : NEWSLETTER SUBSCRIPTION');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('MODULE : NEWSLETTER SUBSCRIPTION');
        expect(csvDatum[0].error).to.equal('Newsletter subscription: no email to export, this customer has not registered.');
      });

      it('should check that Module product comments is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkModuleProductComments', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'MODULE : PRODUCT COMMENTS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('MODULE : PRODUCT COMMENTS');
        expect(csvDatum[0].headers.length).to.equal(0);
        expect(csvDatum[0].rows.length).to.equal(0);
        expect(csvDatum[0].error).to.equal('');
      });

      it('should check that mail alerts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkModuleMailAlerts', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'MODULE : MAIL ALERTS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('MODULE : MAIL ALERTS');
        expect(csvDatum[0].error).to.equal('Mail alert: Unable to export customer using email.');
      });
    });
  });

  describe('Check GDPR CSV file after create a cart', async () => {
    describe('Add a product to the cart and download CSV file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount2', baseContext);

        await foHummingbirdHomePage.goToFo(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        // Go to the first product page
        await foHummingbirdHomePage.goToProductPage(page, 1);
        // Add the product to the cart
        await foHummingbirdProductPage.addProductToTheCart(page, 2);

        const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(2);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage2', baseContext);

        await foHummingbirdHomePage.goToMyAccountPage(page);

        const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage2', baseContext);

        await foHummingbirdMyAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await foHummingbirdMyGDPRPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyGDPRPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV2', baseContext);

        filePath = await foHummingbirdMyGDPRPersonalDataPage.exportDataToCSV(page);

        const found = await utilsFile.doesFileExist(filePath);
        expect(found, 'CSV file was not downloaded').to.eq(true);
      });
    });

    describe('Get shopping cart data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage1', baseContext);

        await foHummingbirdLoginPage.goTo(page, global.BO.URL);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Orders > Shopping carts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.shoppingCartsLink,
        );

        const pageTitle = await boShoppingCartsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
      });

      it('should reset all filters and get number of shopping carts', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

        numberOfShoppingCarts = await boShoppingCartsPage.resetAndGetNumberOfLines(page);
        expect(numberOfShoppingCarts).to.be.above(0);
      });

      it('should filter list by customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

        await boShoppingCartsPage.filterTable(page, 'input', 'customer_name', customerData.lastName);

        const numberOfShoppingCartsAfterFilter = await boShoppingCartsPage.getNumberOfElementInGrid(page);
        expect(numberOfShoppingCartsAfterFilter).to.equal(1);

        const textColumn = await boShoppingCartsPage.getTextColumn(page, 1, 'customer_name');
        expect(textColumn).to.contains(customerData.lastName);
      });

      it('should get shopping cart ID and Date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getShoppingCartIDAndDate', baseContext);

        shoppingCartDate = await boShoppingCartsPage.getTextColumn(page, 1, 'date_add');
        shoppingCartDate = `${shoppingCartDate.substring(6, 10)}-${shoppingCartDate.substring(0, 2)}-`
          + `${shoppingCartDate.substring(3, 5)} ${shoppingCartDate.substring(11, 19)}`;

        shoppingCartID = await boShoppingCartsPage.getTextColumn(page, 1, 'id_cart');
        expect(parseInt(shoppingCartID, 10)).to.be.greaterThan(5);
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check Carts table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatCartsTableIsEmpty2', baseContext);

        csvData = await modPsGdprBoMain.parseCSVFile(filePath, 'utf16le');
        expect(csvData.length).to.gt(0);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'CARTS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('CARTS');
        expect(csvDatum[0].rows[0]).to.deep.equal([
          `#${shoppingCartID}`,
          '1',
          shoppingCartDate,
        ]);
        expect(csvData.length).to.gt(0);

        const csvDatum2 = csvData.filter((item: GDPRExportCSV) => item.title === 'PRODUCT(S) STILL IN CART');
        expect(csvDatum2.length).to.equal(1);
        expect(csvDatum2[0].title).to.equal('PRODUCT(S) STILL IN CART');
        expect(csvDatum2[0].rows[0]).to.deep.equal([
          `#${shoppingCartID}`,
          dataProducts.demo_1.reference,
          dataProducts.demo_1.name,
          '2',
        ]);
      });
    });
  });

  describe('Check GDPR CSV file after create an order and an address', async () => {
    describe('Create an order and download CSV file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount3', baseContext);

        await foHummingbirdHomePage.goToFo(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to carts page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        await foHummingbirdHomePage.goToCartPage(page);

        const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
        expect(pageTitle).to.contains(foHummingbirdCartPage.pageTitle);
      });

      it('should fill address form and go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

        // Proceed to checkout the shopping cart
        await foHummingbirdCartPage.clickOnProceedToCheckout(page);

        const isStepAddressComplete = await foHummingbirdCheckoutPage.setAddress(page, addressData);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await foHummingbirdCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await foHummingbirdCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foHummingbirdCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage3', baseContext);

        await foHummingbirdHomePage.goToMyAccountPage(page);

        const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage3', baseContext);

        await foHummingbirdMyAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await foHummingbirdMyGDPRPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyGDPRPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV3', baseContext);

        filePath = await foHummingbirdMyGDPRPersonalDataPage.exportDataToCSV(page);

        const found = await utilsFile.doesFileExist(filePath);
        expect(found, 'CSV file was not downloaded').to.eq(true);
      });
    });

    describe('Get created order data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage2', baseContext);

        await foHummingbirdLoginPage.goTo(page, global.BO.URL);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should filter the Orders table by customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrdersTable', baseContext);

        await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

        const numberOfOrdersAfterFilter = await boOrdersPage.getNumberOfElementInGrid(page);
        expect(numberOfOrdersAfterFilter).to.equal(1);

        const textColumn = await boOrdersPage.getTextColumn(page, 'customer');
        expect(textColumn).to.contains(customerData.lastName);
      });

      it('should get order data', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderData', baseContext);

        orderReference = await boOrdersPage.getTextColumn(page, 'reference');
        expect(orderReference).to.not.eq(null);

        totalPaid = await boOrdersPage.getOrderATIPrice(page);
        orderDate = await boOrdersPage.getTextColumn(page, 'date_add');
        orderDate = `${orderDate.substring(6, 10)}-${orderDate.substring(0, 2)}-${orderDate.substring(3, 5)}`
          + `${orderDate.substring(11, 19)}`;
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetOrdersTable', baseContext);

        const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
        expect(numberOfOrders).to.be.above(0);
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable1', baseContext);

        csvData = await modPsGdprBoMain.parseCSVFile(filePath, 'utf16le');
        expect(csvData.length).to.gt(0);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'ADDRESSES');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('ADDRESSES');
        expect(csvDatum[0].rows[0]).to.include.ordered.members([
          addressData.alias,
          addressData.company,
          `${addressData.firstName} ${addressData.lastName}`,
          addressData.address,
          addressData.phone,
          addressData.country,
        ]);
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable1', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'ORDERS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('ORDERS');
        expect(csvDatum[0].rows[0]).to.include.ordered.members([
          orderReference,
          'Bank transfer',
          'Awaiting bank wire payment',
          `${totalPaid} EUR`,
        ]);

        const csvDatum2 = csvData.filter((item: GDPRExportCSV) => item.title === 'PRODUCTS BOUGHT');
        expect(csvDatum2.length).to.equal(1);
        expect(csvDatum2[0].title).to.equal('PRODUCTS BOUGHT');
        expect(csvDatum2[0].rows[0]).to.deep.equal([
          orderReference,
          dataProducts.demo_1.reference,
          `${dataProducts.demo_1.name} (Size: S - Color: White)`,
          '2',
        ]);
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable1', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'CARTS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('CARTS');
        expect(csvDatum[0].error).to.equal('No carts');

        const csvDatum2 = csvData.filter((item: GDPRExportCSV) => item.title === 'PRODUCT(S) STILL IN CART');
        expect(csvDatum2.length).to.equal(1);
        expect(csvDatum2[0].title).to.equal('PRODUCT(S) STILL IN CART');
        expect(csvDatum2[0].error).to.equal('No carts');
      });
    });
  });

  describe('Check GDPR CSV file after send a message', async () => {
    describe('Send message and download CSV file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount4', baseContext);

        await foHummingbirdHomePage.goToFo(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go on contact us page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

        // Go to contact us page
        await foHummingbirdLoginPage.goToFooterLink(page, 'Contact us');

        const pageTitle = await foHummingbirdContactUsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdContactUsPage.pageTitle);
      });

      it('should send message to customer service', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

        await foHummingbirdContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

        const validationMessage = await foHummingbirdContactUsPage.getAlertSuccess(page);
        expect(validationMessage).to.equal(foHummingbirdContactUsPage.validationMessage);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage4', baseContext);

        await foHummingbirdHomePage.goToMyAccountPage(page);

        const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage4', baseContext);

        await foHummingbirdMyAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await foHummingbirdMyGDPRPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyGDPRPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV4', baseContext);

        filePath = await foHummingbirdMyGDPRPersonalDataPage.exportDataToCSV(page);

        const found = await utilsFile.doesFileExist(filePath);
        expect(found, 'CSV file was not downloaded').to.eq(true);
      });
    });

    describe('Get message data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage3', baseContext);

        await foHummingbirdLoginPage.goTo(page, global.BO.URL);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to customer service page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customerServiceParentLink,
          boDashboardPage.customerServiceLink,
        );

        const pageTitle = await boCustomerServicePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
      });

      it('should check customer name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerName', baseContext);

        const email = await boCustomerServicePage.getTextColumn(page, 1, 'customer');
        expect(email).to.contain(`${contactUsData.firstName} ${contactUsData.lastName}`);
      });

      it('should get last message date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

        messageDate = await boCustomerServicePage.getTextColumn(page, 1, 'date');
        messageDate = `${messageDate.substring(6, 10)}-${messageDate.substring(0, 2)}-`
          + `${messageDate.substring(3, 5)} ${messageDate.substring(11, 19)}`;
        expect(messageDate).to.not.eq(null);
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable2', baseContext);

        csvData = await modPsGdprBoMain.parseCSVFile(filePath, 'utf16le');
        expect(csvData.length).to.gt(0);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'ADDRESSES');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('ADDRESSES');
        expect(csvDatum[0].rows[0]).to.include.ordered.members([
          addressData.alias,
          addressData.company,
          `${addressData.firstName} ${addressData.lastName}`,
          addressData.address,
          addressData.phone,
          addressData.country,
        ]);
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable2', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'ORDERS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('ORDERS');
        expect(csvDatum[0].rows[0]).to.include.ordered.members([
          orderReference,
          'Bank transfer',
          'Awaiting bank wire payment',
          `${totalPaid} EUR`,
        ]);

        const csvDatum2 = csvData.filter((item: GDPRExportCSV) => item.title === 'PRODUCTS BOUGHT');
        expect(csvDatum2.length).to.equal(1);
        expect(csvDatum2[0].title).to.equal('PRODUCTS BOUGHT');
        expect(csvDatum2[0].rows[0]).to.deep.equal([
          orderReference,
          dataProducts.demo_1.reference,
          `${dataProducts.demo_1.name} (Size: S - Color: White)`,
          '2',
        ]);
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable2', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'CARTS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('CARTS');
        expect(csvDatum[0].error).to.equal('No carts');

        const csvDatum2 = csvData.filter((item: GDPRExportCSV) => item.title === 'PRODUCT(S) STILL IN CART');
        expect(csvDatum2.length).to.equal(1);
        expect(csvDatum2[0].title).to.equal('PRODUCT(S) STILL IN CART');
        expect(csvDatum2[0].error).to.equal('No carts');
      });

      it('should check Messages table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkMessagesTable1', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'MESSAGES');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('MESSAGES');
        expect(csvDatum[0].rows[0]).to.deep.equal([
          ipAddress,
          contactUsData.message,
          messageDate,
        ]);
      });
    });
  });

  describe('Check GDPR CSV file after logout and login in FO', async () => {
    describe('Logout then login and download CSV file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount5', baseContext);

        await foHummingbirdHomePage.goToFo(page);

        const isHomePage = await foHummingbirdHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should logout by the link in the header', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByHeaderLink', baseContext);

        await foHummingbirdHomePage.logout(page);

        const isCustomerConnected = await foHummingbirdHomePage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
      });

      it('should sign in', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInFO2', baseContext);

        await foHummingbirdHomePage.goToLoginPage(page);
        await foHummingbirdLoginPage.customerLogin(page, customerData);

        const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage5', baseContext);

        await foHummingbirdHomePage.goToMyAccountPage(page);

        const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage5', baseContext);

        await foHummingbirdMyAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await foHummingbirdMyGDPRPersonalDataPage.getPageTitle(page);
        expect(pageTitle).to.equal(foHummingbirdMyGDPRPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV5', baseContext);

        filePath = await foHummingbirdMyGDPRPersonalDataPage.exportDataToCSV(page);

        const found = await utilsFile.doesFileExist(filePath);
        expect(found, 'CSV file was not downloaded').to.eq(true);
      });
    });

    describe('Get last customer connection data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage4', baseContext);

        await foHummingbirdLoginPage.goTo(page, global.BO.URL);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Customers > Customers\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage2', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customersParentLink,
          boDashboardPage.customersLink,
        );

        await boCustomersPage.closeSfToolBar(page);

        const pageTitle = await boCustomersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomersPage.pageTitle);
      });

      it(`should filter by customer first name '${customerData.firstName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomerFirstName2', baseContext);

        await boCustomersPage.filterCustomers(page, 'input', 'firstname', customerData.firstName);

        const numberOfCustomersAfterFilter = await boCustomersPage.getNumberOfElementInGrid(page);
        expect(numberOfCustomersAfterFilter).to.equal(1);
      });

      it('should get last visit date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getLastVisitDate2', baseContext);

        const lastVisit = await boCustomersPage.getTextColumnFromTableCustomers(page, 1, 'connect');
        secondLastVisitDate = `${lastVisit.substring(6, 10)}-${lastVisit.substring(0, 2)}-`
          + `${lastVisit.substring(3, 5)} ${lastVisit.substring(11, 19)}`;
        expect(lastVisitDate).to.contains(date.getFullYear());
      });

      it('should click on view customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage2', baseContext);

        await boCustomersPage.goToViewCustomerPage(page, 1);

        const pageTitle = await boCustomersViewPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomersViewPage.pageTitle(createCustomerName));
      });

      it('should get last connections origin', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getLastConnectionsOrigin', baseContext);

        connectionOrigin = await boCustomersViewPage.getTextColumnFromTableLastConnections(page, 'origin', 1);
        if (connectionOrigin === 'Direct link') {
          connectionOrigin = '';
        } else if (connectionOrigin === 'localhost') {
          connectionOrigin = global.INSTALL.ENABLE_SSL ? 'https://localhost:8002/' : 'http://localhost:8001/';
        }
        expect(connectionOrigin).to.not.eq(null);
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable3', baseContext);

        csvData = await modPsGdprBoMain.parseCSVFile(filePath, 'utf16le');
        expect(csvData.length).to.gt(0);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'ADDRESSES');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('ADDRESSES');
        expect(csvDatum[0].rows[0]).to.include.ordered.members([
          addressData.alias,
          addressData.company,
          `${addressData.firstName} ${addressData.lastName}`,
          addressData.address,
          addressData.phone,
          addressData.country,
        ]);
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable3', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'ORDERS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('ORDERS');
        expect(csvDatum[0].rows[0]).to.include.ordered.members([
          orderReference,
          'Bank transfer',
          'Awaiting bank wire payment',
          `${totalPaid} EUR`,
        ]);

        const csvDatum2 = csvData.filter((item: GDPRExportCSV) => item.title === 'PRODUCTS BOUGHT');
        expect(csvDatum2.length).to.equal(1);
        expect(csvDatum2[0].title).to.equal('PRODUCTS BOUGHT');
        expect(csvDatum2[0].rows[0]).to.deep.equal([
          orderReference,
          dataProducts.demo_1.reference,
          `${dataProducts.demo_1.name} (Size: S - Color: White)`,
          '2',
        ]);
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable3', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'CARTS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('CARTS');
        expect(csvDatum[0].error).to.equal('No carts');

        const csvDatum2 = csvData.filter((item: GDPRExportCSV) => item.title === 'PRODUCT(S) STILL IN CART');
        expect(csvDatum2.length).to.equal(1);
        expect(csvDatum2[0].title).to.equal('PRODUCT(S) STILL IN CART');
        expect(csvDatum2[0].error).to.equal('No carts');
      });

      it('should check Messages table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkMessagesTable2', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'MESSAGES');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('MESSAGES');
        expect(csvDatum[0].rows[0]).to.deep.equal([
          ipAddress,
          contactUsData.message,
          messageDate,
        ]);
      });

      it('should check Last connections table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLastConnectionsTable2', baseContext);

        const csvDatum = csvData.filter((item: GDPRExportCSV) => item.title === 'LAST CONNECTIONS');
        expect(csvDatum.length).to.equal(1);
        expect(csvDatum[0].title).to.equal('LAST CONNECTIONS');
        expect(csvDatum[0].rows[0]).to.deep.equal([
          connectionOrigin,
          '0',
          '',
          ipAddress,
          secondLastVisitDate,
        ]);
        expect(csvDatum[0].rows[1]).to.deep.equal([
          '',
          '0',
          '',
          ipAddress,
          lastVisitDate,
        ]);
      });
    });
  });

  // Post-condition: Create new account on FO
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
});
