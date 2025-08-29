import testContext from '@utils/testContext';
import {expect} from 'chai';
import {createEmployeeTest, deleteEmployeeTest} from '@commonTests/BO/advancedParameters/employee';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boCustomerServicePage,
  boCustomerServiceViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerContactMessage,
  FakerEmployee,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  foClassicMyAccountPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsFile,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customerService_customerService_forwardMessage';

describe('BO - Customer Service : Forward message', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const employeeData: FakerEmployee = new FakerEmployee({
    defaultPage: 'Products',
    language: 'English (English)',
    permissionProfile: 'SuperAdmin',
  });

  const contactUsData: FakerContactMessage = new FakerContactMessage({
    subject: 'Customer service',
    emailAddress: employeeData.email,
    reference: '',
  });

  const messageSend: string = 'I want to exchange my product';

  const messageOption: string = `${dataProducts.demo_1.name} (Size: ${dataProducts.demo_1.attributes[0].values[0]} `
    + `- Color: ${dataProducts.demo_1.attributes[1].values[0]})`;

  // Pre-condition: Create new employee
  createEmployeeTest(employeeData, `${baseContext}_preTest_1`);

  // Pre-Condition: Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });

    await utilsFile.generateImage(`${contactUsData.fileName}.jpg`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);

    await utilsFile.deleteFile(`${contactUsData.fileName}.jpg`);
  });

  describe('FO: Send message', async () => {
    // before and after functions
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);
      const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foClassicHomePage.goToHomePage(page);
      const result = await foClassicHomePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should quick view the first product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickViewFirstProduct', baseContext);

      await foClassicHomePage.quickViewProduct(page, 1);

      const isCartModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
      expect(isCartModalVisible).to.equal(true);
    });

    it('should add first product to cart and Proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicModalQuickViewPage.addToCartByQuickView(page);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it('should proceed to checkout and check Step Address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddressStep', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isStepPersonalInformationComplete = await foClassicCheckoutPage.isStepCompleted(
        page,
        foClassicCheckoutPage.personalInformationStepForm,
      );
      expect(isStepPersonalInformationComplete, 'Step Personal information is not complete').to.eq(true);
    });

    it('should validate Step Address and go to Delivery Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryStep', baseContext);

      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should validate Step Delivery and go to Payment Step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should Pay and confirm order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go to order history page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);
      await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

      const pageHeaderTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicMyOrderHistoryPage.pageTitle);
    });

    it('Go to order details ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrderDetails', baseContext);

      await foClassicMyOrderHistoryPage.goToDetailsPage(page);

      const successMessageText = await foClassicMyOrderDetailsPage.addAMessage(page, messageOption, messageSend);
      expect(successMessageText).to.equal(foClassicMyOrderDetailsPage.successMessageText);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox', baseContext);

      expect(newMail.subject).to.contains('Message from a custom');
    });
  });

  describe('BO: Forward message to another employee', async () => {
    const forwardMessageData: FakerContactMessage = new FakerContactMessage({
      employeeName: `${employeeData.firstName.slice(0, 1)}. ${employeeData.lastName}`,
      message: 'Forward message',
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.customerServiceLink,
      );

      const pageTitle = await boCustomerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
    });

    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await boCustomerServicePage.goToViewMessagePage(page);

      const pageTitle = await boCustomerServiceViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServiceViewPage.pageTitle);
    });

    it('should click on forward message button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForwardButton1', baseContext);

      const isModalVisible = await boCustomerServiceViewPage.clickOnForwardMessageButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should forward the message and check the thread', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'forwardMessage1', baseContext);

      await boCustomerServiceViewPage.forwardMessage(page, forwardMessageData);

      const messages = await boCustomerServiceViewPage.getThreadMessages(page);
      expect(messages)
        .to.contains(`${boCustomerServiceViewPage.forwardMessageSuccessMessage} ${employeeData.firstName}`
        + ` ${employeeData.lastName}`)
        .and.contains(forwardMessageData.message);
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm1', baseContext);

      const text = await boCustomerServiceViewPage.getOrdersAndMessagesTimeline(page);
      expect(text).to.contains('Orders and messages timeline')
        .and.contains(`${boCustomerServiceViewPage.forwardMessageSuccessMessage} ${employeeData.firstName}`
        + ` ${employeeData.lastName}`)
        .and.contains(`Comment: ${forwardMessageData.message}`);
    });

    it('should check if the mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMailIsInMailbox2', baseContext);

      expect(newMail.subject).to.contains('Fwd: Customer message');
    });
  });

  describe('BO: Forward message to someone else', async () => {
    const forwardMessageData: FakerContactMessage = new FakerContactMessage({
      employeeName: 'Someone else',
      emailAddress: 'someoneelse@prestashop.com',
      message: 'checkThis',
    });
    it('should click on forward message button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForwardButton2', baseContext);

      const isModalVisible = await boCustomerServiceViewPage.clickOnForwardMessageButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should forward the message and check the thread', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'forwardMessage2', baseContext);

      await boCustomerServiceViewPage.forwardMessage(page, forwardMessageData);

      const messages = await boCustomerServiceViewPage.getThreadMessages(page);
      expect(messages)
        .to.contains(`Message forwarded to ${forwardMessageData.emailAddress}`)
        .and.contains(forwardMessageData.message);
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm2', baseContext);

      const text = await boCustomerServiceViewPage.getOrdersAndMessagesTimeline(page);
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

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.customerServiceLink,
      );

      const pageTitle = await boCustomerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await boCustomerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(boCustomerServicePage.successfulDeleteMessage);
    });
  });

  // Post-condition: Delete employee
  deleteEmployeeTest(employeeData, `${baseContext}_postTest_1`);

  // Post-Condition: Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
