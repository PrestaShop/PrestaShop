// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import mailHelper from '@utils/mailHelper';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createEmployeeTest, deleteEmployeeTest} from '@commonTests/BO/advancedParameters/employee';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import BO pages
import customerServicePage from '@pages/BO/customerService/customerService';
import viewPage from '@pages/BO/customerService/customerService/view';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {loginPage} from '@pages/FO/classic/login';
import Products from '@data/demo/products';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import {orderHistoryPage} from '@pages/FO/classic/myAccount/orderHistory';
import {orderDetailsPage} from '@pages/FO/classic/myAccount/orderDetails';
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {cartPage} from '@pages/FO/classic/cart';
import {quickViewModal} from '@pages/FO/classic/modal/quickView';
import {blockCartModal} from '@pages/FO/classic/modal/blockCart';

// Import data
import PaymentMethods from '@data/demo/paymentMethods';
import MessageData from '@data/faker/message';
import EmployeeData from '@data/faker/employee';
import type MailDevEmail from '@data/types/maildevEmail';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import MailDev from 'maildev';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customerService_customerService_forwardMessage';

describe('BO - Customer Service : Forward message', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const employeeData = new EmployeeData({
    defaultPage: 'Products',
    language: 'English (English)',
    permissionProfile: 'SuperAdmin',
  });

  const contactUsData: MessageData = new MessageData({
    subject: 'Customer service',
    emailAddress: employeeData.email,
    reference: '',
  });

  const messageSend: string = 'I want to exchange my product';

  const messageOption: string = `${Products.demo_1.name} (Size: ${Products.demo_1.attributes[0].values[0]} `
    + `- Color: ${Products.demo_1.attributes[1].values[0]})`;

  // Pre-condition: Create new employee
  createEmployeeTest(employeeData, `${baseContext}_preTest_1`);

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });

    await files.generateImage(`${contactUsData.fileName}.jpg`);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);

    await files.deleteFile(`${contactUsData.fileName}.jpg`);
  });

  describe('FO: Send message', async () => {
    // before and after functions
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageHeaderTitle = await loginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(loginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await loginPage.customerLogin(page, dataCustomers.johnDoe);
      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await homePage.goToHomePage(page);
      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewFirstProduct', baseContext);

      await homePage.quickViewProduct(page, 1);

      const isCartModalVisible = await quickViewModal.isQuickViewProductModalVisible(page);
      expect(isCartModalVisible).to.equal(true);
    });

    it('should add first product to cart and Proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await quickViewModal.addToCartByQuickView(page);
      await blockCartModal.proceedToCheckout(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should proceed to checkout and check Step Address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isStepPersonalInformationComplete = await checkoutPage.isStepCompleted(
        page,
        checkoutPage.personalInformationStepForm,
      );
      expect(isStepPersonalInformationComplete, 'Step Personal information is not complete').to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);

      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should validate Step Delivery and go to Payment Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should Pay and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await homePage.goToMyAccountPage(page);
      await myAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await orderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(orderHistoryPage.pageTitle);
    });

    it('Go to order details ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await orderHistoryPage.goToDetailsPage(page);

      const successMessageText = await orderDetailsPage.addAMessage(page, messageOption, messageSend);
      expect(successMessageText).to.equal(orderDetailsPage.successMessageText);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox', baseContext);

      expect(newMail.subject).to.contains('Message from a custom');
    });
  });

  describe('BO: Forward message to another employee', async () => {
    const forwardMessageData: MessageData = new MessageData({
      employeeName: `${employeeData.firstName.slice(0, 1)}. ${employeeData.lastName}`,
      message: 'Forward message',
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await customerServicePage.goToViewMessagePage(page);

      const pageTitle = await viewPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewPage.pageTitle);
    });

    it('should click on forward message button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForwardButton1', baseContext);

      const isModalVisible = await viewPage.clickOnForwardMessageButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should forward the message and check the thread', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'forwardMessage1', baseContext);

      await viewPage.forwardMessage(page, forwardMessageData);

      const messages = await viewPage.getThreadMessages(page);
      expect(messages)
        .to.contains(`${viewPage.forwardMessageSuccessMessage} ${employeeData.firstName}`
        + ` ${employeeData.lastName}`)
        .and.contains(forwardMessageData.message);
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm1', baseContext);

      const text = await viewPage.getOrdersAndMessagesTimeline(page);
      expect(text).to.contains('Orders and messages timeline')
        .and.contains(`${viewPage.forwardMessageSuccessMessage} ${employeeData.firstName}`
        + ` ${employeeData.lastName}`)
        .and.contains(`Comment: ${forwardMessageData.message}`);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox2', baseContext);

      expect(newMail.subject).to.contains('Fwd: Customer message');
    });
  });

  describe('BO: Forward message to someone else', async () => {
    const forwardMessageData: MessageData = new MessageData({
      employeeName: 'Someone else',
      emailAddress: 'someoneelse@prestashop.com',
      message: 'checkThis',
    });
    it('should click on forward message button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForwardButton2', baseContext);

      const isModalVisible = await viewPage.clickOnForwardMessageButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should forward the message and check the thread', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'forwardMessage2', baseContext);

      await viewPage.forwardMessage(page, forwardMessageData);

      const messages = await viewPage.getThreadMessages(page);
      expect(messages)
        .to.contains(`Message forwarded to ${forwardMessageData.emailAddress}`)
        .and.contains(forwardMessageData.message);
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm2', baseContext);

      const text = await viewPage.getOrdersAndMessagesTimeline(page);
      expect(text).to.contains('Orders and messages timeline')
        .and.contains(`Message forwarded to ${forwardMessageData.emailAddress}`)
        .and.contains(`Comment: ${forwardMessageData.message}`);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox3', baseContext);

      expect(newMail.subject).to.contains('Fwd: Customer message');
    });
  });

  describe('BO : Delete the message', async () => {
    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPageToDelete', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });

  // Post-condition: Delete employee
  deleteEmployeeTest(employeeData, `${baseContext}_postTest_1`);

  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
