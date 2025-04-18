import testContext from '@utils/testContext';
import {expect} from 'chai';
import {faker} from '@faker-js/faker';

// Import commonTests
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boCustomerServicePage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  foClassicProductPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_userAccount_orderHistory_orderDetails_sendMessage';

/*
Pre-condition:
- Setup SMTP config
Scenario:
- Go to FO and connect to an account
- Go to account page and Order history and details
- Select an order with an invoice
- Click on details
- Select a product and write a message
- Click to send
- Check received email
- Go to BO, Customers service, customers service, the message is displayed
Post-condition:
- Reset SMTP config
 */

describe('FO - Account : Send a message with an ordered product', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  const messageSend: string = faker.lorem.sentence().substring(0, 35).trim();
  const messageOption: string = `${dataProducts.demo_1.name} (Size: ${dataProducts.demo_1.attributes[0].values[0]} `
    + `- Color: ${dataProducts.demo_1.attributes[1].values[0]})`;

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe('Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrder', baseContext);

      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFoToOrder', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFoToOrder', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should create an order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOrder', baseContext);

      // Go to home page
      await foClassicLoginPage.goToHomePage(page);
      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);
      // Add the created product to the cart
      await foClassicProductPage.addProductToTheCart(page);
      // Proceed to checkout the shopping cart
      await foClassicCartPage.clickOnProceedToCheckout(page);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

      // Payment step - Choose payment step
      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);

      // Check the confirmation message
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighOutFO', baseContext);

      await foClassicCheckoutOrderConfirmationPage.logout(page);

      const isCustomerConnected = await foClassicCheckoutOrderConfirmationPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });

  describe('Check invoice file in BO', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to the orders page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should update order status to '${dataOrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.paymentAccepted);
      expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);

      const orderStatus = await boOrdersPage.getTextColumn(page, 'osname', 1);
      expect(orderStatus, 'Order status was not updated').to.equal(dataOrderStatuses.paymentAccepted.name);
    });

    it('disconnect from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logoutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });
  });

  describe('Send a message on order history', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      await foClassicHomePage.goToFo(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('Should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('should go to order details and add a comment', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foClassicMyOrderHistoryPage.goToDetailsPage(page);

      const successMessageText = await foClassicMyOrderDetailsPage.addAMessage(page, messageOption, messageSend);
      expect(successMessageText).to.equal(foClassicMyOrderDetailsPage.successMessageText);
    });

    it('should check the received email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(allEmails[numberOfEmails - 1].subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Message from a customer`);
      expect(allEmails[numberOfEmails - 1].text).to.contains('You have received a new message')
        .and.to.contains(
          `Customer: ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName} (${dataCustomers.johnDoe.email})`,
        )
        .and.to.contains(messageSend);
    });
  });

  describe('Check message in BO', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

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
      expect(email).to.contain(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);
    });

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const email = await boCustomerServicePage.getTextColumn(page, 1, 'a!email');
      expect(email).to.contain(dataCustomers.johnDoe.email);
    });

    it('should check message type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageType', baseContext);

      const email = await boCustomerServicePage.getTextColumn(page, 1, 'cl!id_contact');
      expect(email).to.contain('--');
    });

    it('should check message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const email = await boCustomerServicePage.getTextColumn(page, 1, 'message');
      expect(email).to.contain(messageSend);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await boCustomerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(boCustomerServicePage.successfulDeleteMessage);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest`);
});
