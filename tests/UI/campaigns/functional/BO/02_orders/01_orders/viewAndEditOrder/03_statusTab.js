require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const mailHelper = require('@utils/mailHelper');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const loginPage = require('@pages/BO/login/index');
const dashboardPage = require('@pages/BO/dashboard');
const emailPage = require('@pages/BO/advancedParameters/email');
const employeesPage = require('@pages/BO/advancedParameters/team/index');
const addEmployeePage = require('@pages/BO/advancedParameters/team/add');
const customersPage = require('@pages/BO/customers');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');

// Import faker data
const EmployeeFaker = require('@data/faker/employee');
const AddressFaker = require('@data/faker/address');
const CustomerFaker = require('@data/faker/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_login_passwordReminder';

let browserContext;
let page;
let numberOfEmployees = 0;

// maildev config and vars
let newMail;
const {smtpServer, smtpPort} = global.maildevConfig;
const resetPasswordMailSubject = 'Your new password';

// new employee data
const createEmployeeData = new EmployeeFaker({
  defaultPage: 'Products',
  language: 'English (English)',
  permissionProfile: 'SuperAdmin',
});

const addressData = new AddressFaker({country: 'France'});
const customerData = new CustomerFaker({password: ''});

// mailListener
let mailListener;

describe('BO - Orders - view and edit order : Check order status block', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);
    // Handle every new email
    mailListener.on('new', (email) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  // Pre-condition - Setup smtp parameters
  describe('Go to BO to setup the smtp parameters', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > E-mail\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailSetupPageForSetupSmtpParams', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.emailLink,
      );

      await emailPage.closeSfToolBar(page);

      const pageTitle = await emailPage.getPageTitle(page);
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should fill the smtp parameters form fields', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillSmtpParametersFormField', baseContext);

      const alertSuccessMessage = await emailPage.setupSmtpParameters(
        page,
        smtpServer,
        DefaultCustomer.email,
        DefaultCustomer.password,
        smtpPort,
      );

      await expect(alertSuccessMessage).to.contains(emailPage.successfulUpdateMessage);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  // Pre-condition - Create new employee
  describe('Go to BO and create a new employee', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Team\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTeamPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.teamLink,
      );

      const pageTitle = await employeesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(employeesPage.pageTitle);
    });

    it('should reset all filters and get number of employees', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfEmployees = await employeesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfEmployees).to.be.above(0);
    });

    it('should go to add new employee page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewEmployeePage', baseContext);

      await employeesPage.goToAddNewEmployeePage(page);
      const pageTitle = await addEmployeePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addEmployeePage.pageTitleCreate);
    });

    it('should create employee and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createEmployee', baseContext);

      const textResult = await addEmployeePage.createEditEmployee(page, createEmployeeData);
      await expect(textResult).to.equal(employeesPage.successfulCreationMessage);
    });

    it('should logout from BO', async function () {
      await loginCommon.logoutBO(this, page);
    });
  });

  // Pre-condition - Create order by guest
  describe('Create order by guest in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHomePage.goToFo(page);
      await foHomePage.changeLanguage(page, 'en');

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHomePage.goToHomePage(page);

      // Go to the fourth product page
      await foHomePage.goToProductPage(page, 4);

      // Add the created product to the cart
      await foProductPage.addProductToTheCart(page, 1);

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

  // Pre-condition - Create order by default customer
  describe('Create order by default customer in FO', async () => {
    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foHomePage.goToHomePage(page);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should add product to cart and proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      await foHomePage.goToHomePage(page);

      // Go to the first product page
      await foHomePage.goToProductPage(page, 1);

      // Add the product to the cart
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

      // Check the confirmation message
      const cardTitle = await foOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(foOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  // 1 - View order page
  describe('Go to view order page', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Check status tab
  describe('Check status tab', async () => {
    it('should check the status number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStatusNumber', baseContext);

      const statusNumber = await viewOrderPage.getStatusNumber(page);
      await expect(statusNumber).to.be.equal(1);
    });

    it('should check that the order note is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNote', baseContext);

      const isOpened = await viewOrderPage.isOrderNoteOpened(page);
      await expect(isOpened).to.be.false;
    });

    it('should click on \'Resend email\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resendEmail', baseContext);

      const textResult = await viewOrderPage.resendEmail(page);
      await expect(textResult).to.contains(viewOrderPage.validationSendMessage);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfResetPasswordMailIsInMailbox', baseContext);

      await expect(newMail.subject).to.contains('[PrestaShop] Awaiting bank wire payment');
    });
  });

  // Post-condition - Delete employee
  /* describe('Delete created employee', async () => {
     it('should login in BO', async function () {
       await loginCommon.loginBO(this, page);
     });

     it('should go to \'Advanced Parameters > Team\' page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPageToDelete', baseContext);

       await dashboardPage.goToSubMenu(
         page,
         dashboardPage.advancedParametersLink,
         dashboardPage.teamLink,
       );

       const pageTitle = await employeesPage.getPageTitle(page);
       await expect(pageTitle).to.contains(employeesPage.pageTitle);
     });

     it('should filter list of employees by email', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'filterEmployeesToDelete', baseContext);

       await employeesPage.filterEmployees(
         page,
         'input',
         'email',
         createEmployeeData.email,
       );

       const textEmail = await employeesPage.getTextColumnFromTable(page, 1, 'email');
       await expect(textEmail).to.contains(createEmployeeData.email);
     });

     it('should delete employee', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'deleteEmployee', baseContext);

       const textResult = await employeesPage.deleteEmployee(page, 1);
       await expect(textResult).to.equal(employeesPage.successfulDeleteMessage);
     });

     it('should reset filter and check the number of employees', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

       const numberOfEmployeesAfterDelete = await employeesPage.resetAndGetNumberOfLines(page);
       await expect(numberOfEmployeesAfterDelete).to.be.equal(numberOfEmployees);
     });
   });

   // Post-condition - Delete guest account
   describe('Delete the created guest account', async () => {
     it('should go \'Customers >  Customers\' page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

       await dashboardPage.goToSubMenu(page, dashboardPage.customersParentLink, dashboardPage.customersLink);

       await customersPage.closeSfToolBar(page);

       const pageTitle = await customersPage.getPageTitle(page);
       await expect(pageTitle).to.contains(customersPage.pageTitle);
     });

     it('should filter list by customer email', async function () {
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

   // Post-condition - Reset default email parameters
   describe('Go to BO and reset to default mail parameters', async () => {
     it('should go to \'Advanced Parameters > E-mail\' page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToEmailSetupPageForResetSmtpParams', baseContext);

       await dashboardPage.goToSubMenu(
         page,
         dashboardPage.advancedParametersLink,
         dashboardPage.emailLink,
       );

       await emailPage.closeSfToolBar(page);

       const pageTitle = await emailPage.getPageTitle(page);
       await expect(pageTitle).to.contains(emailPage.pageTitle);
     });

     it('should reset parameters', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'resetMailParameters', baseContext);

       const successParametersReset = await emailPage.resetDefaultParameters(page);
       await expect(successParametersReset).to.contains(emailPage.successfulUpdateMessage);
     });

     it('should logout from BO', async function () {
       await loginCommon.logoutBO(this, page);
     });
   });*/
});
