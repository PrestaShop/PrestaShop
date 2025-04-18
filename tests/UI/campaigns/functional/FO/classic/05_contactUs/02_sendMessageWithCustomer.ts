import testContext from '@utils/testContext';
import {expect} from 'chai';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

import {
  boCustomerServicePage,
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  dataOrders,
  FakerContactMessage,
  foClassicContactUsPage,
  foClassicHomePage,
  foClassicLoginPage,
  type MailDev,
  type MailDevEmail,
  modContactFormBoMain,
  type Page,
  utilsFile,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_contactUs_sendMessageWithCustomer';

/*
Pre-condition:
- Setup SMTP parameters
- Configure contact form module
Scenario:
Go to FO
Log in with default customer
Send a message on contact page
Verify email
Verify message on customer service page
Post-condition:
- Reset config in Contact form module
- Reset SMTP parameters
 */
describe('FO - Contact us : Send message from contact us page with customer logged in', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const contactUsData: FakerContactMessage = new FakerContactMessage({
    firstName: dataCustomers.johnDoe.firstName,
    lastName: dataCustomers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: dataCustomers.johnDoe.email,
    reference: dataOrders.order_1.reference,
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.createFile('.', `${contactUsData.fileName}.txt`, 'new filename');

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(`${contactUsData.fileName}.txt`);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe('PRE-TEST: Configure Contact form module', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.contactForm.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.contactForm);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.contactForm.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.contactForm.tag);

      const pageTitle = await modContactFormBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modContactFormBoMain.pageTitle);
    });

    it('should enable Send confirmation email to your customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableSendConfirmationEmail', baseContext);

      const successMessage = await modContactFormBoMain.setSendConfirmationEmail(page, true);
      expect(successMessage).to.contains(modContactFormBoMain.successfulUpdateMessage);
    });

    it('should enable Receive customers\' messages by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReceiveMessagesByEmail', baseContext);

      const successMessage = await modContactFormBoMain.setReceiveCustomersMessageByEmail(page, true);
      expect(successMessage).to.contains(modContactFormBoMain.successfulUpdateMessage);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logOutBO', baseContext);

      await boDashboardPage.logoutBO(page);

      const pageTitle = await boLoginPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLoginPage.pageTitle);
    });
  });

  describe('FO - Send message from contact us page with customer logged in', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFo', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFo', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go on contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

      // Go to contact us page
      await foClassicLoginPage.goToFooterLink(page, 'Contact us');

      const pageTitle = await foClassicContactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicContactUsPage.pageTitle);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await foClassicContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

      const validationMessage = await foClassicContactUsPage.getAlertSuccess(page);
      expect(validationMessage).to.equal(foClassicContactUsPage.validationMessage);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail', baseContext);

      // Translated message looks like this 'Your message no. ct%thread_id% has been correctly sent (thread ID tc%thread_token%)'
      // so we check the two parts that are not dynamic
      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your message`);
      expect(newMail.subject).to.contains('has been correctly sent');
    });
  });

  describe('BO - Check in Customer Service Page the received message and delete it', async () => {
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
      expect(email).to.contain(`${contactUsData.firstName} ${contactUsData.lastName}`);
    });

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const email = await boCustomerServicePage.getTextColumn(page, 1, 'a!email');
      expect(email).to.contain(contactUsData.emailAddress);
    });

    it('should check message type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageType', baseContext);

      const subject = await boCustomerServicePage.getTextColumn(page, 1, 'cl!id_contact');
      expect(subject).to.contain(contactUsData.subject);
    });

    it('should check message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const message = await boCustomerServicePage.getTextColumn(page, 1, 'message');
      expect(message).to.contain(contactUsData.message);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await boCustomerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(boCustomerServicePage.successfulDeleteMessage);
    });
  });

  // Post-Condition : Reset contact form module
  describe('POST-TEST: Reset Contact form module', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.contactForm.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule2', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.contactForm);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.contactForm.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage2', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.contactForm.tag);

      const pageTitle = await modContactFormBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modContactFormBoMain.pageTitle);
    });

    it('should disable Send confirmation email to your customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableSendConfirmationEmail', baseContext);

      const successMessage = await modContactFormBoMain.setSendConfirmationEmail(page, false);
      expect(successMessage).to.contains(modContactFormBoMain.successfulUpdateMessage);
    });

    it('should disable Receive customers\' messages by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableReceiveMessagesByEmail', baseContext);

      const successMessage = await modContactFormBoMain.setReceiveCustomersMessageByEmail(page, false);
      expect(successMessage).to.contains(modContactFormBoMain.successfulUpdateMessage);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_2`);
});
