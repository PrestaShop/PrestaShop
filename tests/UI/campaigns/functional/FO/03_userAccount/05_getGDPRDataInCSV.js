require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const createAccountPage = require('@pages/FO/myAccount/add');
const myAccountPage = require('@pages/FO/myAccount');
const gdprPersonalDataPage = require('@pages/FO/myAccount/gdprPersonalData');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
const checkoutPage = require('@pages/FO/checkout');
const contactUsPage = require('@pages/FO/contactUs');

// Import BO pages
const loginCommon = require('@commonTests/BO/loginBO');
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const shoppingCartsPage = require('@pages/BO/orders/shoppingCarts');
const ordersPage = require('@pages/BO/orders');
const customerServicePage = require('@pages/BO/customerService/customerService');
const viewCustomerPage = require('@pages/BO/customers/view');

// Import common tests
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// Import data
const CustomerFaker = require('@data/faker/customer');
const {Products} = require('@data/demo/products');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const AddressFaker = require('@data/faker/address');
const ContactUsFakerData = require('@data/faker/contactUs');

const baseContext = 'functional_FO_userAccount_getGDPRDataInCSV';

let browserContext;
let page;
const customerData = new CustomerFaker({firstName: 'Marc', lastName: 'Beier', email: 'presta@prestashop.com'});

let filePath;
let registrationDate;
let lastVisitDate;
let secondLastVisitDate;
let numberOfShoppingCarts;
let shoppingCartID;
let shoppingCartDate;
let orderReference;
let totalPaid;
let orderDate;
let messageDate;
let ipAddress;
let connectionOrigin;

const date = new Date();

const addressData = new AddressFaker({
  firstName: 'Marc',
  lastName: 'Beier',
  country: 'France',
  address: '17, Main street',
  city: 'Paris',
  company: 'PrestaShop',
});

const contactUsData = new ContactUsFakerData(
  {
    firstName: customerData.firstName,
    lastName: customerData.lastName,
    subject: 'Customer service',
    message: 'Message test',
    emailAddress: customerData.email,
    reference: orderReference,
  },
);

/*
Scenario
- Check GDPR CSV file after create customer and first login
- Check GDPR CSV file after create a cart
- Check GDPR CSV file after create an order and an address
- Check GDPR CSV file after send a message
- Check GDPR CSV file after logout and login in FO
Post condition:
- Delete created customer
 */
describe('FO - Account : Get GDPR data in CSV', async () => {
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

  describe('Check GDPR CSV file after create customer and first login', async () => {
    describe('Create account on FO and download GDPR - Personal data CSV', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount1', baseContext);

        await homePage.goToFo(page);
        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage).to.be.true;
      });

      it('should go to create account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

        await homePage.goToLoginPage(page);
        await loginPage.goToCreateAccountPage(page);

        const pageHeaderTitle = await createAccountPage.getHeaderTitle(page);
        await expect(pageHeaderTitle).to.equal(createAccountPage.formTitle);
      });

      it('should create new account', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

        await createAccountPage.createAccount(page, customerData);

        const isCustomerConnected = await homePage.isCustomerConnected(page);
        await expect(isCustomerConnected).to.be.true;
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage1', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage1', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        await expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV1', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToCSV(page);

        const found = await files.doesFileExist(filePath);
        await expect(found, 'CSV file was not downloaded').to.be.true;
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
        await expect(pageTitle).to.contains(customersPage.pageTitle);
      });

      it(`should filter by customer first name '${customerData.firstName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomerFirstName1', baseContext);

        await customersPage.filterCustomers(page, 'input', 'firstname', customerData.firstName);

        const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCustomersAfterFilter).to.equal(1);
      });

      it('should get creation account date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getRegistrationDate', baseContext);

        const registration = await customersPage.getTextColumnFromTableCustomers(page, 1, 'date_add');

        registrationDate = `${registration.substr(6, 4)}-${registration.substr(0, 2)}-`
          + `${registration.substr(3, 2)}${registration.substr(11, 8)}`;
        await expect(registrationDate).to.contains(date.getFullYear());
      });

      it('should get last visit date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getLastVisitDate1', baseContext);

        const lastVisit = await customersPage.getTextColumnFromTableCustomers(page, 1, 'connect');
        lastVisitDate = `${lastVisit.substr(6, 4)}-${lastVisit.substr(0, 2)}-`
          + `${lastVisit.substr(3, 2)}${lastVisit.substr(11, 8)}`;
        await expect(lastVisitDate).to.contains(date.getFullYear());
      });

      it('should click on view customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);

        await customersPage.goToViewCustomerPage(page, 1);

        const pageTitle = await viewCustomerPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewCustomerPage.pageTitle);
      });

      it('should get last connections ip address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLAstConnections', baseContext);

        ipAddress = await viewCustomerPage.getTextColumnFromTableLastConnections(page, 'ip-address');
        await expect(ipAddress).to.not.be.null;
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check general info', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkGeneralInfo', baseContext);

        // TODO
        // To replace isVisible after release of the module psgdpr
        // const age = await basicHelper.age(customerData.birthDate);

        // const isVisible = await files.isTextInFile(
        //   filePath,
        //   '"GENERALINFO"GenderName"Birthdate"AgeEmailLanguage"Creationaccountdata""Lastvisit"'
        //   + `SiretApeCompanyWebsite${customerData.socialTitle}"${customerData.firstName}${customerData.lastName}"`
        //   + `${customerData.birthDate.toISOString().slice(0, 10)})${age}${customerData.email}"English(English)""`
        //   + `${registrationDate}""${lastVisitDate}`,
        //   true, true, 'utf16le');

        let isVisible = await files.isTextInFile(
          filePath,
          '"GENERALINFO"GenderName"Birthdate"AgeEmailLanguage"Creationaccountdata""Lastvisit"'
          + `SiretApeCompanyWebsite${customerData.socialTitle}"${customerData.firstName}${customerData.lastName}"`
          + `${customerData.birthDate.toISOString().slice(0, 10)}`,
          true, true, 'utf16le',
        );
        await expect(isVisible, 'General info is not correct!').to.be.true;

        isVisible = await files.isTextInFile(
          filePath,
          `${customerData.email}"English(English)""${registrationDate}""${lastVisitDate}`,
          true, true, 'utf16le',
        );
        await expect(isVisible, 'General info is not correct!').to.be.true;
      });

      it('should check that Addresses table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatAddressesTableIsEmpty', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'ADDRESSESAliasCompanyNameAddressPhone(s)CountryDate"Noaddresses"',
          true, true, 'utf16le',
        );
        await expect(isVisible, 'Addresses table is not empty!').to.be.true;
      });

      it('should check that Orders table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatOrdersTableIsEmpty', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'RDERSReferencePayment"Orderstate""Totalpaid"Date"Noorders"',
          true, true, 'utf16le',
        );
        await expect(isVisible, 'Orders table is not empty!').to.be.true;
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatCartsTableIsEmpty1', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'CARTSId"Totalproducts"Date"Nocarts""PRODUCT(S)STILLINCART""CartID""Productreference"NameQuantity"Nocarts"',
          true, true, 'utf16le',
        );
        await expect(isVisible, 'Carts table is not empty!').to.be.true;
      });

      it('should check that Messages table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatMessagesTableIsEmpty', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'MESSAGESIPMessageDate"Nomessages""',
          true, true, 'utf16le',
        );
        await expect(isVisible, 'Messages table is not empty!').to.be.true;
      });

      it('should check Last connections table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLastConnectionsTable1', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `LASTCONNECTIONS""Originrequest""Pageviewed""Timeonthepage""IPaddress"DateCountryDate0${ipAddress}`
          + `"${lastVisitDate}"`,
          true, true, 'utf16le',
        );
        await expect(isVisible, 'The data in Last connections table is not correct!').to.be.true;
      });

      it('should check that Newsletter subscription table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNewsletterSubscriptionTable', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          '"MODULE:NEWSLETTERSUBSCRIPTION""Newslettersubscription:noemailtoexport,thiscustomerhasnotregistered.""',
          true, true, 'utf16le',
        );
        await expect(isVisible, 'Newsletter subscription table is not empty!').to.be.true;
      });

      it('should check that Module product comments is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkModuleProductComments', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          '""MODULE:PRODUCTCOMMENTS""MODULE:MAILALERTS"',
          true, true, 'utf16le',
        );
        await expect(isVisible, 'Products comments is not empty!').to.be.true;
      });

      it('should check that mail alerts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkModuleMailAlerts', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'MODULE:MAILALERTS""Mailalert:Unabletoexportcustomerusingemail."',
          true, true, 'utf16le',
        );
        await expect(isVisible, 'Mail alert table is not empty!').to.be.true;
      });
    });
  });

  describe('Check GDPR CSV file after create a cart', async () => {
    describe('Add a product to the cart and download CSV file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount2', baseContext);

        await homePage.goToFo(page);
        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage).to.be.true;
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        // Go to the first product page
        await homePage.goToProductPage(page, 1);

        // Add the product to the cart
        await productPage.addProductToTheCart(page, 2);

        const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber).to.be.equal(2);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage2', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage2', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        await expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV2', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToCSV(page);

        const found = await files.doesFileExist(filePath);
        await expect(found, 'CSV file was not downloaded').to.be.true;
      });
    });

    describe('Get shopping cart data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage1', baseContext);

        await loginPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        await expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to \'Orders > Shopping carts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.shoppingCartsLink,
        );

        const pageTitle = await shoppingCartsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
      });

      it('should reset all filters and get number of shopping carts', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

        numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfShoppingCarts).to.be.above(0);
      });

      it('should filter list by customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

        await shoppingCartsPage.filterTable(page, 'input', 'c!lastname', customerData.lastName);

        const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
        await expect(numberOfShoppingCartsAfterFilter).to.equal(1);

        const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'total');
        await expect(textColumn).to.contains(customerData.lastName);
      });

      it('should get shopping cart ID and Date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getShoppingCartIDAndDate', baseContext);

        shoppingCartDate = await shoppingCartsPage.getTextColumn(page, 1, 'id_guest');
        shoppingCartDate = `${shoppingCartDate.substr(6, 4)}-${shoppingCartDate.substr(0, 2)}-`
          + `${shoppingCartDate.substr(3, 2)}${shoppingCartDate.substr(11, 8)}`;

        shoppingCartID = await shoppingCartsPage.getTextColumn(page, 1, 'status');
        await expect(parseInt(shoppingCartID, 10)).to.be.greaterThan(5);
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check Carts table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatCartsTableIsEmpty2', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `CARTSId"Totalproducts"Date#${shoppingCartID}1`
          + `"${shoppingCartDate}""PRODUCT(S)STILLINCART""CartID""Productreference"NameQuantity`
          + `#${shoppingCartID}${Products.demo_1.reference}"${Products.demo_1.name.replace(/\s/g, '')}"2`,
          true, true, 'utf16le',
        );
        await expect(isVisible, 'Data in Carts table is not correct!').to.be.true;
      });
    });
  });

  describe('Check GDPR CSV file after create an order and an address', async () => {
    describe('Create an order and download CSV file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount3', baseContext);

        await homePage.goToFo(page);
        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage).to.be.true;
      });

      it('should go to carts page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        await homePage.goToCartPage(page);

        const pageTitle = await cartPage.getPageTitle(page);
        await expect(pageTitle).to.contains(cartPage.pageTitle);
      });

      it('should fill address form and go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setAddressStep', baseContext);

        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage3', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage3', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        await expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV3', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToCSV(page);

        const found = await files.doesFileExist(filePath);
        await expect(found, 'CSV file was not downloaded').to.be.true;
      });
    });

    describe('Get created order data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage2', baseContext);

        await loginPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        await expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should filter the Orders table by customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrdersTable', baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

        const numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
        await expect(numberOfOrdersAfterFilter).to.equal(1);

        const textColumn = await ordersPage.getTextColumn(page, 'customer');
        await expect(textColumn).to.contains(customerData.lastName);
      });

      it('should get order data', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderData', baseContext);

        orderReference = await ordersPage.getTextColumn(page, 'reference');
        await expect(orderReference).to.not.be.null;

        totalPaid = await ordersPage.getOrderATIPrice(page);
        orderDate = await ordersPage.getTextColumn(page, 'date_add');
        orderDate = `${orderDate.substr(6, 4)}-${orderDate.substr(0, 2)}-${orderDate.substr(3, 2)}`
          + `${orderDate.substr(11, 8)}`;
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetOrdersTable', baseContext);

        const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfOrders).to.be.above(0);
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable1', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `ADDRESSESAliasCompanyNameAddressPhone(s)CountryDate"MyAddress"${addressData.company}`
          + `"${addressData.firstName}${addressData.lastName}""${addressData.address.replace(/\s/g, '')}"`
          + `"${addressData.phone}"${addressData.country}"`,
          true, true, 'utf16le');
        await expect(isVisible, 'Data in Addresses table is not correct!').to.be.true;
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable1', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `ORDERSReferencePayment"Orderstate""Totalpaid"Date${orderReference}"Banktransfer"`
          + `"Awaitingbankwirepayment""${totalPaid}EUR""${orderDate}""PRODUCTSBOUGHT""Orderref""Productref"`
          + `NameQuantity${orderReference}${Products.demo_1.reference}"${Products.demo_1.name.replace(/\s/g, '')}`
          + '(Size:S-Color:White)"2',
          true, true, 'utf16le');
        await expect(isVisible, 'Data in Orders table is not correct!').to.be.true;
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable1', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'CARTSId"Totalproducts"Date"Nocarts""PRODUCT(S)STILLINCART""CartID""Productreference"NameQuantity"Nocarts"',
          true, true, 'utf16le');
        await expect(isVisible, 'Carts table is not empty!').to.be.true;
      });
    });
  });

  describe('Check GDPR CSV file after send a message', async () => {
    describe('Send message and download CSV file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount4', baseContext);

        await homePage.goToFo(page);
        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage).to.be.true;
      });

      it('should go on contact us page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

        // Go to contact us page
        await loginPage.goToFooterLink(page, 'Contact us');

        const pageTitle = await contactUsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(contactUsPage.pageTitle);
      });

      it('should send message to customer service', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

        await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

        const validationMessage = await contactUsPage.getAlertSuccess(page);
        await expect(validationMessage).to.equal(contactUsPage.validationMessage);
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage4', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage4', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        await expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV4', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToCSV(page);

        const found = await files.doesFileExist(filePath);
        await expect(found, 'CSV file was not downloaded').to.be.true;
      });
    });

    describe('Get message data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage3', baseContext);

        await loginPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        await expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to customer service page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customerServiceParentLink,
          dashboardPage.customerServiceLink,
        );

        const pageTitle = await customerServicePage.getPageTitle(page);
        await expect(pageTitle).to.contains(customerServicePage.pageTitle);
      });

      it('should check customer name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerName', baseContext);

        const email = await customerServicePage.getTextColumn(page, 1, 'customer');
        await expect(email).to.contain(`${contactUsData.firstName} ${contactUsData.lastName}`);
      });

      it('should get last message date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

        messageDate = await customerServicePage.getTextColumn(page, 1, 'date');
        messageDate = `${messageDate.substr(6, 4)}-${messageDate.substr(0, 2)}-`
          + `${messageDate.substr(3, 2)}${messageDate.substr(11, 8)}`;
        await expect(messageDate).to.not.be.null;
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable2', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `ADDRESSESAliasCompanyNameAddressPhone(s)CountryDate"MyAddress"${addressData.company}`
          + `"${addressData.firstName}${addressData.lastName}""${addressData.address.replace(/\s/g, '')}"`
          + `"${addressData.phone}"${addressData.country}"`,
          true, true, 'utf16le');
        await expect(isVisible, 'Data in Addresses table is not correct!').to.be.true;
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable2', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `ORDERSReferencePayment"Orderstate""Totalpaid"Date${orderReference}"Banktransfer"`
          + `"Awaitingbankwirepayment""${totalPaid}EUR""${orderDate}""PRODUCTSBOUGHT""Orderref""Productref"`
          + `NameQuantity${orderReference}${Products.demo_1.reference}"${Products.demo_1.name.replace(/\s/g, '')}`
          + '(Size:S-Color:White)"2',
          true, true, 'utf16le');
        await expect(isVisible, 'Data in Orders table is not correct!').to.be.true;
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable2', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'CARTSId"Totalproducts"Date"Nocarts""PRODUCT(S)STILLINCART""CartID""Productreference"NameQuantity"Nocarts"',
          true, true, 'utf16le');
        await expect(isVisible, 'Carts table is not empty!').to.be.true;
      });

      it('should check Messages table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkMessagesTable1', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `MESSAGESIPMessageDate${ipAddress}"${contactUsData.message.replace(/\s/g, '')}""${messageDate}`,
          true, true, 'utf16le');
        await expect(isVisible, 'Data in Messages table is not correct!').to.be.true;
      });
    });
  });

  describe('Check GDPR CSV file after logout and login in FO', async () => {
    describe('Logout then login and download CSV file', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount5', baseContext);

        await homePage.goToFo(page);
        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage).to.be.true;
      });

      it('should logout by the link in the header', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByHeaderLink', baseContext);

        await homePage.logout(page);

        const isCustomerConnected = await homePage.isCustomerConnected(page);
        await expect(isCustomerConnected, 'Customer is connected!').to.be.false;
      });

      it('should sign in', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInFO2', baseContext);

        await homePage.goToLoginPage(page);
        await loginPage.customerLogin(page, customerData);

        const isCustomerConnected = await loginPage.isCustomerConnected(page);
        await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage5', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go to \'GDPR - Personal data\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage5', baseContext);

        await myAccountPage.goToMyGDPRPersonalDataPage(page);

        const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
        await expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
      });

      it('should click on \'Get my data to CSV file\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV5', baseContext);

        filePath = await gdprPersonalDataPage.exportDataToCSV(page);

        const found = await files.doesFileExist(filePath);
        await expect(found, 'CSV file was not downloaded').to.be.true;
      });
    });

    describe('Get last customer connection data from BO', async () => {
      it('should open the BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBoPage4', baseContext);

        await loginPage.goTo(page, global.BO.URL);

        const pageTitle = await dashboardPage.getPageTitle(page);
        await expect(pageTitle).to.contains(dashboardPage.pageTitle);
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
        await expect(pageTitle).to.contains(customersPage.pageTitle);
      });

      it(`should filter by customer first name '${customerData.firstName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomerFirstName2', baseContext);

        await customersPage.filterCustomers(page, 'input', 'firstname', customerData.firstName);

        const numberOfCustomersAfterFilter = await customersPage.getNumberOfElementInGrid(page);
        await expect(numberOfCustomersAfterFilter).to.equal(1);
      });

      it('should get last visit date', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getLastVisitDate2', baseContext);

        const lastVisit = await customersPage.getTextColumnFromTableCustomers(page, 1, 'connect');
        secondLastVisitDate = `${lastVisit.substr(6, 4)}-${lastVisit.substr(0, 2)}-`
          + `${lastVisit.substr(3, 2)}${lastVisit.substr(11, 8)}`;
        await expect(lastVisitDate).to.contains(date.getFullYear());
      });

      it('should click on view customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage2', baseContext);

        await customersPage.goToViewCustomerPage(page, 1);
        const pageTitle = await viewCustomerPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewCustomerPage.pageTitle);
      });

      it('should get last connections origin', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getLastConnectionsOrigin', baseContext);

        connectionOrigin = await viewCustomerPage.getTextColumnFromTableLastConnections(page, 'origin', 1);
        if (connectionOrigin === 'Direct link') {
          connectionOrigin = '';
        } else if (connectionOrigin === 'localhost') {
          connectionOrigin = 'http://localhost:8001/en/';
        }
        await expect(connectionOrigin).to.not.be.null;
      });
    });

    describe('Check GDPR data in CSV', async () => {
      it('should check Addresses table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAddressesTable3', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `ADDRESSESAliasCompanyNameAddressPhone(s)CountryDate"MyAddress"${addressData.company}`
          + `"${addressData.firstName}${addressData.lastName}""${addressData.address.replace(/\s/g, '')}"`
          + `"${addressData.phone}"${addressData.country}"`,
          true, true, 'utf16le');
        await expect(isVisible, 'Data in Addresses table is not correct!').to.be.true;
      });

      it('should check Orders table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersTable3', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `ORDERSReferencePayment"Orderstate""Totalpaid"Date${orderReference}"Banktransfer"`
          + `"Awaitingbankwirepayment""${totalPaid}EUR""${orderDate}""PRODUCTSBOUGHT""Orderref""Productref"`
          + `NameQuantity${orderReference}${Products.demo_1.reference}"${Products.demo_1.name.replace(/\s/g, '')}`
          + '(Size:S-Color:White)"2',
          true, true, 'utf16le');
        await expect(isVisible, 'Data in Orders table is not correct!').to.be.true;
      });

      it('should check that Carts table is empty', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCartsTable3', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'CARTSId"Totalproducts"Date"Nocarts""PRODUCT(S)STILLINCART""CartID""Productreference"NameQuantity"Nocarts"',
          true, true, 'utf16le');
        await expect(isVisible, 'Carts table is not empty!').to.be.true;
      });

      it('should check Messages table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkMessagesTable2', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          `MESSAGESIPMessageDate${ipAddress}"${contactUsData.message.replace(/\s/g, '')}""${messageDate}`,
          true, true, 'utf16le');
        await expect(isVisible, 'Data in Messages table is not correct!').to.be.true;
      });

      it('should check Last connections table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLastConnectionsTable2', baseContext);

        const isVisible = await files.isTextInFile(
          filePath,
          'LASTCONNECTIONS""Originrequest""Pageviewed""Timeonthepage""IPaddress"DateCountryDate'
          + `${connectionOrigin}0${ipAddress}"${secondLastVisitDate}"0${ipAddress}"${lastVisitDate}"`,
          true, true, 'utf16le');
        await expect(isVisible, 'The data in Last connections table is not correct!').to.be.true;
      });
    });
  });

  // Post-condition: Create new account on FO
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
