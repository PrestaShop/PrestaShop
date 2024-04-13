// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import mailHelper from '@utils/mailHelper';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import customerServicePage from '@pages/BO/customerService/customerService';
import contactFormPage from '@pages/BO/modules/contactForm';
import {moduleManager} from '@pages/BO/modules/moduleManager';

// Import FO pages
import contactUsPage from '@pages/FO/hummingbird/contactUs';
import homePage from '@pages/FO/hummingbird/home';
import loginPage from '@pages/FO/hummingbird/login';

// Import data
import MessageData from '@data/faker/message';
import MailDevEmail from '@data/types/maildevEmail';
import Modules from '@data/demo/modules';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import MailDev from 'maildev';

const baseContext: string = 'functional_FO_hummingbird_contactUs_sendMessageAsAnonymous';

/*
Pre-condition:
- Setup SMTP parameters
- Configure contact form module
Scenario:
Go to FO
Check if not connected
Check errors
Send a message on contact page
Verify email
Go to BO
Verify message on customer service page
Post-condition:
- Reset config in Contact form module
- Reset SMTP parameters
 */
describe('FO - Contact us : Send message from contact us page with customer not logged', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const contactUsEmptyEmail: MessageData = new MessageData({
    firstName: dataCustomers.johnDoe.firstName,
    lastName: dataCustomers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: '',
  });
  const contactUsInvalidEmail: MessageData = new MessageData({
    firstName: dataCustomers.johnDoe.firstName,
    lastName: dataCustomers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: 'demo@prestashop',
  });
  const contactUsEmptyContent: MessageData = new MessageData({
    firstName: dataCustomers.johnDoe.firstName,
    lastName: dataCustomers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: dataCustomers.johnDoe.email,
    message: '',
  });
  const contactUsData: MessageData = new MessageData({
    firstName: dataCustomers.johnDoe.firstName,
    lastName: dataCustomers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: dataCustomers.johnDoe.email,
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.createFile('.', `${contactUsData.fileName}.txt`, 'new filename');

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile(`${contactUsData.fileName}.txt`);
    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  describe('PRE-TEST: Configure Contact form module', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it(`should search the module ${Modules.contactForm.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, Modules.contactForm);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${Modules.contactForm.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManager.goToConfigurationPage(page, Modules.contactForm.tag);

      const pageTitle = await contactFormPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(contactFormPage.pageTitle);
    });

    it('should enable Send confirmation email to your customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableSendConfirmationEmail', baseContext);

      const successMessage = await contactFormPage.setSendConfirmationEmail(page, true);
      expect(successMessage).to.contains(contactFormPage.successfulUpdateMessage);
    });

    it('should enable Receive customers\' messages by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReceiveMessagesByEmail', baseContext);

      const successMessage = await contactFormPage.setReceiveCustomersMessageByEmail(page, true);
      expect(successMessage).to.contains(contactFormPage.successfulUpdateMessage);
    });

    it('should logout from BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'logOutBO', baseContext);

      await loginCommon.logoutBO(this, page);
    });
  });

  describe('FO - Send message from contact us page with customer not logged', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should check if that any account is connected', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfCustomerNotConnected', baseContext);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
    });

    it('should go on contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

      // Go to contact us page
      await loginPage.goToFooterLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(contactUsPage.pageTitle);
    });

    it('should check if the email is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmptyEmail', baseContext);

      await contactUsPage.sendMessage(page, contactUsEmptyEmail);

      const invalidEmailError = await contactUsPage.getAlertError(page);
      expect(invalidEmailError).to.contains(contactUsPage.invalidEmail);
    });

    it('should check if the email is invalid', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvalidEmail', baseContext);

      await contactUsPage.sendMessage(page, contactUsInvalidEmail);

      const invalidEmailError = await contactUsPage.getAlertError(page);
      expect(invalidEmailError).to.contains(contactUsPage.invalidEmail);
    });

    it('should check if the content is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmptyContent', baseContext);

      await contactUsPage.sendMessage(page, contactUsEmptyContent);

      const invalidEmailError = await contactUsPage.getAlertError(page);
      expect(invalidEmailError).to.contains(contactUsPage.invalidContent);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

      const validationMessage = await contactUsPage.getAlertSuccess(page);
      expect(validationMessage).to.equal(contactUsPage.validationMessage);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail', baseContext);

      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] Your message has been correctly sent`);
    });
  });

  describe('BO - Check in Customer Service Page the received message and delete it', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
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

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'a!email');
      expect(email).to.contain(contactUsData.emailAddress);
    });

    it('should check message type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageType', baseContext);

      const subject = await customerServicePage.getTextColumn(page, 1, 'cl!id_contact');
      expect(subject).to.contain(contactUsData.subject);
    });

    it('should check message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const message = await customerServicePage.getTextColumn(page, 1, 'message');
      expect(message).to.contain(contactUsData.message);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });

  // Post-Condition : Reset contact form module
  describe('POST-TEST: Reset Contact form module', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it(`should search the module ${Modules.contactForm.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule2', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, Modules.contactForm);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${Modules.contactForm.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage2', baseContext);

      await moduleManager.goToConfigurationPage(page, Modules.contactForm.tag);

      const pageTitle = await contactFormPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(contactFormPage.pageTitle);
    });

    it('should disable Send confirmation email to your customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableSendConfirmationEmail', baseContext);

      const successMessage = await contactFormPage.setSendConfirmationEmail(page, false);
      expect(successMessage).to.contains(contactFormPage.successfulUpdateMessage);
    });

    it('should disable Receive customers\' messages by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableReceiveMessagesByEmail', baseContext);

      const successMessage = await contactFormPage.setReceiveCustomersMessageByEmail(page, false);
      expect(successMessage).to.contains(contactFormPage.successfulUpdateMessage);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_1`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_2`);
});
